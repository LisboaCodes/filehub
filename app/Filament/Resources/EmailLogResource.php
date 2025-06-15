<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmailLogResource\Pages;
use App\Models\EmailLog;
use Filament\Resources\Resource;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;

class EmailLogResource extends Resource
{
    protected static ?string $model = EmailLog::class;
    // Ícone válido presente no Heroicons Outline
    protected static ?string $navigationIcon = 'heroicon-o-inbox';
    protected static ?string $navigationLabel = 'Logs de E-mail';
    protected static ?string $pluralModelLabel = 'Logs de E-mail';
    protected static ?string $navigationGroup = 'Sistema';

    public static function canCreate(): bool
    {
        return true;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->label('Usuário')
                    ->required(),

                TextInput::make('assunto')
                    ->label('Assunto')
                    ->required()
                    ->maxLength(150),

                Textarea::make('mensagem')
                    ->label('Mensagem')
                    ->required()
                    ->rows(5),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Usuário')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('assunto')
                    ->label('Assunto')
                    ->wrap(),

                TextColumn::make('mensagem')
                    ->label('Mensagem')
                    ->wrap()
                    ->limit(50),

                TextColumn::make('created_at')
                    ->label('Enviado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([])
            ->actions([])
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListEmailLogs::route('/'),
            'create' => Pages\CreateEmailLog::route('/create'),
        ];
    }
}
