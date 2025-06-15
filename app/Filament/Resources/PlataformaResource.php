<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlataformaResource\Pages;
use App\Models\Plataforma;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;

class PlataformaResource extends Resource
{
    protected static ?string $model = Plataforma::class;

    // --- NAVEGAÇÃO ---
    protected static ?string $navigationIcon = 'heroicon-o-server-stack';
    protected static ?string $modelLabel = 'Status de Bot';
    protected static ?string $pluralModelLabel = 'Status de Bots';
    protected static ?string $navigationGroup = 'Sistema';
    protected static ?int $navigationSort = 102;

    // Remove o botão "Criar Novo", pois os bots são definidos no arquivo de config
    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('nome')->disabled(),
                TextInput::make('bot_identifier')->label('Identificador do Bot')->disabled(),
                Select::make('status')
                    ->options([
                        'Online' => 'Online',
                        'Manutenção' => 'Manutenção',
                        'Desativado' => 'Desativado',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nome')->searchable(),
                TextColumn::make('bot_identifier')->label('Identificador'),
                // BadgeColumn para um status visualmente mais claro
                BadgeColumn::make('status')
                    ->colors([
                        'success' => 'Online',
                        'warning' => 'Manutenção',
                        'danger' => 'Desativado',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            // Removemos as ações em massa, pois não fazem sentido aqui
            ->bulkActions([]);
    }
    
    // ... getRelations e getPages continuam iguais
    public static function getRelations(): array { return []; }
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPlataformas::route('/'),
            'edit' => Pages\EditPlataforma::route('/{record}/edit'),
        ];
    }
}