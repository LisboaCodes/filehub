<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MenuResource\Pages;
use App\Models\Menu;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

// Componentes do Formulário
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;

// Componentes da Tabela
use Filament\Tables\Columns\TextColumn;

class MenuResource extends Resource
{
    protected static ?string $model = Menu::class;

    // --- CONFIGURAÇÃO DA NAVEGAÇÃO ---
    protected static ?string $navigationIcon = 'heroicon-o-bars-3'; // Ícone mais apropriado para menu
    protected static ?string $modelLabel = 'Menu';
    protected static ?string $pluralModelLabel = 'Menus';
    protected static ?string $navigationGroup = 'Gestão de Conteúdo'; // Mesmo grupo do resource "Páginas"
    protected static ?int $navigationSort = 2; // Aparecerá depois de "Páginas"

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Usando Section para organizar o formulário visualmente
                Section::make('Detalhes do Item de Menu')
                    ->schema([
                        TextInput::make('label')
                            ->label('Texto do Link (Label)')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('url')
                            ->label('URL de Destino')
                            ->placeholder('Ex: /contato ou https://google.com')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('ordem')
                            ->label('Ordem de Exibição')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->helperText('Itens com números menores aparecem primeiro.'),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('ordem')
                    ->label('Ordem')
                    ->sortable(), // Permite clicar para ordenar

                TextColumn::make('label')
                    ->label('Texto do Link')
                    ->searchable(), // Permite buscar por este campo

                TextColumn::make('url')
                    ->label('URL de Destino')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            // AQUI ESTÁ A MÁGICA: Habilita a reordenação por arrastar e soltar
            // O 'ordem' informa qual coluna deve ser atualizada com a nova posição.
            ->reorderable('ordem');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMenus::route('/'),
            'create' => Pages\CreateMenu::route('/create'),
            'edit' => Pages\EditMenu::route('/{record}/edit'),
        ];
    }
}