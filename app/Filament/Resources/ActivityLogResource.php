<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActivityLogResource\Pages;
use App\Models\ActivityLog;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model; // Importante para a correção
use Illuminate\Support\Facades\DB;

class ActivityLogResource extends Resource
{
    protected static ?string $model = ActivityLog::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationGroup = 'Sistema';
    protected static ?string $modelLabel = 'Log de Atividades';
    protected static ?string $pluralModelLabel = 'Logs de Atividades';
    protected static ?int $navigationSort = 104;

    public static function canCreate(): bool
    {
        return false;
    }

    // A CORREÇÃO ESTÁ AQUI: Trocamos "ActivityLog $record" por "Model $record"
    public static function canEdit(Model $record): bool
    {
        return false;
    }

    // Define a visualização dos dados
public static function infolist(Infolist $infolist): Infolist
{
    return $infolist->schema([
        Section::make('Detalhes do Log')->schema([
            TextEntry::make('created_at')->label('Data')->dateTime('d/m/Y H:i:s'),
            TextEntry::make('event_type')->label('Tipo de Evento'),
            TextEntry::make('user.name')->label('Usuário'),
            TextEntry::make('description')->label('Descrição'),
        ]),
        Section::make('Dados Adicionais (JSON)')
            ->schema([
                // AQUI ESTÁ A CORREÇÃO
                TextEntry::make('properties')
                    ->label('')
                    // Usamos formatStateUsing para converter manualmente o array em um texto JSON formatado
                    ->formatStateUsing(fn ($state): string => json_encode($state, JSON_PRETTY_PRINT))
                    // Adicionamos a tag <pre> para que o navegador respeite a formatação e quebras de linha
                    ->html()
                    ->getStateUsing(fn ($record) => $record->properties),
            ])->collapsible(),
    ]);
}

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')->label('Data')->dateTime('d/m/Y H:i')->sortable(),
                Tables\Columns\TextColumn::make('event_type')->label('Tipo de Evento')->badge()->searchable(),
                Tables\Columns\TextColumn::make('description')->label('Descrição')->searchable()->limit(60),
                Tables\Columns\TextColumn::make('user.name')->label('Usuário')->searchable(),
                Tables\Columns\TextColumn::make('properties.ip_address')->label('Endereço IP')->searchable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('user')->label('Usuário')->relationship('user', 'name'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label("Apagar Selecionados"),
                ]),
            ])
            ->headerActions([
                Tables\Actions\Action::make('downloadLogs')
                    ->label('Baixar Logs (JSON)')
                    ->action(function ($livewire) {
                        $data = $livewire->getFilteredTableQuery()->get()->toJson();
                        return response()->streamDownload(
                            fn() => print($data),
                            'logs-'.now()->format('Y-m-d').'.json'
                        );
                    }),
                Tables\Actions\Action::make('deleteAll')->label('Apagar Todos os Logs')->color('danger')->requiresConfirmation()
                    ->action(fn() => ActivityLog::query()->delete()),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListActivityLogs::route('/'),
            'view' => Pages\ViewActivityLog::route('/{record}'),
        ];
    }
}