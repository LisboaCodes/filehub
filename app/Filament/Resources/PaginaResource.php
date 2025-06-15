<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaginaResource\Pages;
use App\Models\Pagina;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

// Componentes do Formulário
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Group;

// Componentes da Tabela
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;

class PaginaResource extends Resource
{
    protected static ?string $model = Pagina::class;

    // --- CONFIGURAÇÃO DA NAVEGAÇÃO ---
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $modelLabel = 'Página';
    protected static ?string $pluralModelLabel = 'Páginas';
    protected static ?string $navigationGroup = 'Gestão de Conteúdo'; // Agrupa sob este título
    protected static ?int $navigationSort = 1; // Define a ordem no menu

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Grupo principal para layout
                Group::make()->schema([
                    Section::make('Conteúdo Principal')->schema([
                        TextInput::make('title')
                            ->label('Título da Página')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),

                        RichEditor::make('content')
                            ->label('Conteúdo')
                            ->required()
                            ->columnSpanFull(),
                    ])
                ])->columnSpan(['lg' => 2]),

                // Grupo da barra lateral para configurações
                Group::make()->schema([
                    Section::make('Publicação')->schema([
                        TextInput::make('slug')
                            ->label('URL (Slug)')
                            ->required()
                            ->maxLength(255)
                            ->disabled()
                            ->dehydrated()
                            ->unique(Pagina::class, 'slug', ignoreRecord: true),
                        
                        Toggle::make('is_published')
                            ->label('Publicada')
                            ->helperText('Se ativo, a página estará visível para os usuários do site.')
                            ->default(true),
                    ])
                ])->columnSpan(['lg' => 1]),
                
            ])->columns(3); // Define um layout de 3 colunas para o formulário
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Título')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('slug')
                    ->label('URL'),
                IconColumn::make('is_published')
                    ->label('Publicada')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->label('Última Atualização')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPaginas::route('/'),
            'create' => Pages\CreatePagina::route('/create'),
            'edit' => Pages\EditPagina::route('/{record}/edit'),
        ];
    }
}