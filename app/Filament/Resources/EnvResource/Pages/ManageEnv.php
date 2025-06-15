<?php

namespace App\Filament\Resources\EnvResource\Pages;

use App\Filament\Resources\EnvResource;
use App\Models\EnvLog;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class ManageEnv extends Page implements Forms\Contracts\HasForms
{
    use InteractsWithForms;

    protected static string $resource = EnvResource::class;
    protected static string $view = 'filament.resources.env-resource.pages.manage-env';

    public array $data = [];
    public string $security_pin = '';

    public function mount(): void
    {
        $this->form->fill([
            'data' => $this->getEnvArray(),
        ]);
    }

    private function getEnvArray(): array
    {
        $lines = File::exists(base_path('.env'))
            ? File::lines(base_path('.env'))
            : [];

        $env = [];
        foreach ($lines as $line) {
            if (trim($line) === '' || str_starts_with($line, '#')) {
                continue;
            }
            [$key, $value] = explode('=', $line, 2);
            $env[$key] = trim($value);
        }

        return $env;
    }

    public function save(): void
    {
        $user = Auth::user();
        if (! $user || $user->security_pin !== $this->security_pin) {
            Notification::make()
                ->title('PIN de segurança inválido')
                ->danger()
                ->send();

            return;
        }

        $before = $this->getEnvArray();
        $after  = $this->form->getState()['data'];

        $content = collect($after)
            ->map(fn($value, $key) => "$key=$value")
            ->implode(PHP_EOL);

        File::put(base_path('.env'), $content);

        EnvLog::create([
            'user_id' => $user->id,
            'before'  => $before,
            'after'   => $after,
        ]);

        Notification::make()
            ->title('.env atualizado com sucesso')
            ->success()
            ->send();
    }

    public function createBackup(): void
    {
        $timestamp = now()->format('Ymd-His');
        $dir       = storage_path('app/env_backups');
        File::ensureDirectoryExists($dir);
        $filename = "env-{$timestamp}.bak";
        $backup  = "{$dir}/{$filename}";
        File::copy(base_path('.env'), $backup);

        Notification::make()
            ->title('Backup criado com sucesso')
            ->success()
            ->send();
    }

    public function restoreBackup(string $filename): void
    {
        $path = storage_path("app/env_backups/{$filename}");
        if (! File::exists($path)) {
            Notification::make()
                ->title('Backup não encontrado')
                ->danger()
                ->send();

            return;
        }
        File::copy($path, base_path('.env'));

        Notification::make()
            ->title('Backup restaurado com sucesso')
            ->success()
            ->send();
    }

    public function deleteBackup(string $filename): void
    {
        $path = storage_path("app/env_backups/{$filename}");
        if (File::exists($path)) {
            File::delete($path);
            Notification::make()
                ->title('Backup deletado')
                ->success()
                ->send();
        }
    }

    public function getBackups(): array
    {
        $dir = storage_path('app/env_backups');
        if (! File::exists($dir)) {
            return [];
        }

        return collect(File::files($dir))
            ->sortByDesc(fn ($file) => $file->getMTime())
            ->map(fn ($file) => $file->getFilename())
            ->values()
            ->toArray();
    }

    protected function getFormSchema(): array
    {
        return [
            Section::make('Variáveis do .env')
                ->description('Edite com cuidado. Confirmação por PIN é obrigatória.')
                ->schema([
                    KeyValue::make('data')
                        ->keyLabel('Chave')
                        ->valueLabel('Valor')
                        ->addable()
                        ->editableKeys()
                        ->deletable(),

                    TextInput::make('security_pin')
                        ->label('PIN de Segurança')
                        ->password()
                        ->required()
                        ->helperText('Informe seu PIN de segurança para salvar.'),
                ]),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('Salvar')
                ->label('💾 Salvar .env')
                ->action('save')
                ->requiresConfirmation()
                ->modalHeading('Confirmar atualização do .env')
                ->modalDescription('Tem certeza que deseja salvar essas alterações?')
                ->color('success'),

            Action::make('Backup')
                ->label('📦 Fazer Backup')
                ->action('createBackup')
                ->color('gray'),
        ];
    }

    public function getFooter(): ?View
    {
        return view('filament.resources.env-resource.pages.partials.backup-list', [
            'backups'   => $this->getBackups(),
            'component' => $this,
        ]);
    }
}
