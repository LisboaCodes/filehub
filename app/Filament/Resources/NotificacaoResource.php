<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NotificacaoResource\Pages;
use App\Models\Notificacao;
use App\Models\User;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;

class NotificacaoResource extends Resource
{
    protected static ?string $model = Notificacao::class;

    protected static ?string $navigationIcon = 'heroicon-o-bell-alert';
    protected static ?string $navigationGroup = 'Gestão de Usuários';
    protected static ?string $modelLabel = 'Notificação Enviada';
    protected static ?string $pluralModelLabel = 'Notificações Enviadas';
    protected static ?int $navigationSort = 5;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Detalhes da Notificação')
                    ->schema([
                        Infolists\Components\TextEntry::make('title')->label('Título'),
                        Infolists\Components\TextEntry::make('sender.name')->label('Remetente'),
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('created_at')->label('Data de Envio')->dateTime('d/m/Y H:i'),
                                Infolists\Components\TextEntry::make('recipients_count')->label('Total de Destinatários'),
                            ]),
                    ]),
                Infolists\Components\Section::make('Conteúdo')
                    ->schema([
                        // CORREÇÃO: O método correto na v3 é ->html()
                        Infolists\Components\TextEntry::make('content')->label('')->html(),
                    ]),
                Infolists\Components\Section::make('Anexo')
                    ->schema([
                        Infolists\Components\ImageEntry::make('attachment_path')
                            ->label('')
                            ->disk('public')
                            ->visible(fn ($state) => $state && \Illuminate\Support\Str::is(['*.png', '*.jpg', '*.jpeg', '*.gif'], $state)),
                        
                        Infolists\Components\TextEntry::make('attachment_path')
                            ->label('')
                            ->formatStateUsing(fn ($state) => 'Clique para baixar o anexo PDF')
                            ->url(fn ($state) => $state ? \Illuminate\Support\Facades\Storage::disk('public')->url($state) : null)
                            ->openUrlInNewTab()
                            ->visible(fn ($state) => $state && \Illuminate\Support\Str::is('*.pdf', $state)),
                    ])
                    ->visible(fn (Notificacao $record) => $record->attachment_path),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(
                Notificacao::withCount([
                    'recipients',
                    'recipients as read_count' => fn ($query) => $query->whereNotNull('notificacao_user.read_at')
                ])
            )
            ->columns([
                Tables\Columns\TextColumn::make('title')->label('Título')->searchable()->limit(40),
                Tables\Columns\TextColumn::make('sender.name')->label('Remetente')->searchable(),
                Tables\Columns\TextColumn::make('read_count')->label('Leituras')->formatStateUsing(fn ($record) => "{$record->read_count} de {$record->recipients_count}")->sortable(),
                Tables\Columns\TextColumn::make('created_at')->label('Data de Envio')->dateTime('d/m/Y H:i')->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('Apagar Selecionadas'),
                ]),
            ])
            ->headerActions([
                Tables\Actions\Action::make('deleteAll')
                    ->label('Apagar Todas as Notificações')
                    ->requiresConfirmation()
                    ->modalDescription('Tem certeza que deseja apagar TODAS as notificações? Esta ação é irreversível.')
                    ->color('danger')
                    ->action(function () {
                        DB::table('notificacao_user')->truncate();
                        Notificacao::query()->truncate();
                        \Filament\Notifications\Notification::make()->title('Todas as notificações foram apagadas.')->success()->send();
                    })
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNotificacaos::route('/'),
            'view' => Pages\ViewNotificacao::route('/{record}'),
        ];
    }
}