<?php

namespace App\Filament\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class GerenciarBackups extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-server-stack';
    protected static string $view             = 'filament.pages.gerenciar-backups';
    protected static ?string $navigationGroup = 'Sistema';
    protected static ?string $title           = 'Backups do Sistema';
    protected static ?int    $navigationSort  = 103;

    /** Pasta onde os arquivos .sql serão salvos */
    public string $backupPath = '';

    /* -----------------------------------------------------------------
     |  Lifecycle
     | -----------------------------------------------------------------
     */

    public function mount(): void
    {
        $folder          = config('app.name', 'Filehub');
        $this->backupPath = storage_path("app/{$folder}");
        File::ensureDirectoryExists($this->backupPath);
    }

    /* -----------------------------------------------------------------
     |  Header action – Gerar backup
     | -----------------------------------------------------------------
     */

    protected function getHeaderActions(): array
    {
        return [
            Action::make('createBackup')
                ->label('Gerar Novo Backup')
                ->icon('heroicon-o-plus-circle')
                ->color('success')
                ->visible(fn () => auth()->id() === 1)
                ->form([
                    ToggleButtons::make('backup_type')
                        ->label('Tipo de Backup')
                        ->required()
                        ->default('completo')
                        ->reactive()
                        ->options([
                            'completo'   => 'Completo',
                            'especifica' => 'Tabela Específica',
                        ]),
                    Select::make('table_name')
                        ->label('Selecione a Tabela')
                        ->options($this->getTablesList())
                        ->searchable()
                        ->visible(fn (Get $get) => $get('backup_type') === 'especifica')
                        ->required(fn (Get $get) => $get('backup_type') === 'especifica'),
                ])
                ->action(fn (array $data) => $this->createBackupAction($data)),
        ];
    }

    public function createBackupAction(array $data): void
    {
        try {
            $type      = $data['backup_type'];
            $tableName = $data['table_name'] ?? null;
            $timestamp = now()->format('Y-m-d_H-i-s');

            $filename  = $type === 'completo'
                ? "backup-completo-{$timestamp}.sql"
                : "backup-tabela-{$tableName}-{$timestamp}.sql";

            $filePath  = "{$this->backupPath}/{$filename}";
            $handle    = fopen($filePath, 'w+');

            $database  = DB::getDatabaseName();

            /* Cabeçalho + desligar FK */
            fwrite($handle,
                "-- Backup {$type} do Banco: {$database}\n"
              . "-- Gerado em: " . now()->toDateTimeString() . "\n\n"
              . "SET FOREIGN_KEY_CHECKS=0;\n\n"
            );

            /* Quais tabelas exportar? */
            $tables = ($type === 'completo' || ! $tableName)
                ? collect(DB::select('SHOW TABLES'))
                    ->map(fn ($t) => $t->{"Tables_in_{$database}"})
                : [$tableName];

            foreach ($tables as $tbl) {
                if (in_array($tbl, ['migrations', 'jobs', 'failed_jobs', 'sessions'])) {
                    continue; // ignora tabelas de sistema
                }

                /* Estrutura */
                fwrite($handle, "DROP TABLE IF EXISTS `{$tbl}`;\n");
                $create = DB::select("SHOW CREATE TABLE `{$tbl}`")[0]->{'Create Table'};
                fwrite($handle, "{$create};\n\n");

                /* Dados */
                $rows = DB::table($tbl)->get();
                if ($rows->isNotEmpty()) {
                    fwrite($handle, "INSERT INTO `{$tbl}` VALUES \n");
                    foreach ($rows as $idx => $row) {
                        $values    = array_map(
                            fn ($v) => is_null($v) ? 'NULL' : "'" . addslashes((string) $v) . "'",
                            (array) $row
                        );
                        $terminator = $idx < $rows->count() - 1 ? "),\n" : ");\n\n";
                        fwrite($handle, '(' . implode(', ', $values) . $terminator);
                    }
                }
            }

            /* Religa FK */
            fwrite($handle, "\nSET FOREIGN_KEY_CHECKS=1;\n");
            fclose($handle);

            Notification::make()->title('Backup criado com sucesso!')->success()->send();
        } catch (\Throwable $e) {
            Notification::make()
                ->title('Falha ao criar backup')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    /* -----------------------------------------------------------------
     |  Listing helpers
     | -----------------------------------------------------------------
     */

    public function getBackups(): array
    {
        if (! File::exists($this->backupPath)) {
            return [];
        }

        return collect(File::files($this->backupPath))
            ->filter(fn ($f) => $f->getExtension() === 'sql')
            ->sortByDesc(fn ($f) => $f->getMTime())
            ->map(fn ($f) => [
                'filename' => $f->getFilename(),
                'label'    => $this->parseBackupLabel($f->getFilename()),
                'date'     => date('d/m/Y H:i:s', $f->getMTime()),
                'size'     => number_format($f->getSize() / 1024 / 1024, 2) . ' MB',
            ])
            ->values()
            ->toArray();
    }

    private function parseBackupLabel(string $filename): string
    {
        if (str_starts_with($filename, 'backup-completo')) {
            return 'Completo';
        }

        if (
            preg_match(
                '/backup-tabela-(.+?)-\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2}\.sql$/',
                $filename,
                $match
            )
        ) {
            return 'Tabela: ' . $match[1];
        }

        return $filename; // Fallback
    }

    private function getTablesList(): array
    {
        $db = DB::getDatabaseName();

        $tables = collect(DB::select('SHOW TABLES'))
            ->map(fn ($t) => $t->{"Tables_in_{$db}"})
            ->toArray();

        return array_combine($tables, $tables);
    }

    /* -----------------------------------------------------------------
     |  Ações (download / restauro / exclusão)
     | -----------------------------------------------------------------
     */

    public function downloadBackup(string $filename, string $security_pin)
    {
        if (auth()->id() !== 1 || auth()->user()->security_pin !== $security_pin) {
            Notification::make()->title('Acesso Negado ou PIN Incorreto!')->danger()->send();
            return;
        }

        return response()->download("{$this->backupPath}/{$filename}");
    }

    public function restoreBackup(string $filename, string $security_pin)
    {
        if (auth()->id() !== 1 || auth()->user()->security_pin !== $security_pin) {
            Notification::make()->title('Acesso Negado ou PIN Incorreto!')->danger()->send();
            return;
        }

        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');

            DB::unprepared(File::get("{$this->backupPath}/{$filename}"));

            Notification::make()->title('Restauração Concluída!')->success()->send();
        } catch (\Throwable $e) {
            Notification::make()
                ->title('Falha na Restauração')
                ->body($e->getMessage())
                ->danger()
                ->send();
        } finally {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }
    }

    public function deleteBackup(string $filename, string $security_pin)
    {
        if (auth()->id() !== 1 || auth()->user()->security_pin !== $security_pin) {
            Notification::make()->title('Acesso Negado ou PIN Incorreto!')->danger()->send();
            return;
        }

        File::delete("{$this->backupPath}/{$filename}");
        Notification::make()->title('Backup excluído com sucesso.')->success()->send();
    }
}
