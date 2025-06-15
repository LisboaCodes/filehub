<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmailTemplateResource\Pages;
use App\Models\EmailTemplate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

// Imports organizados
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\HtmlString;

class EmailTemplateResource extends Resource
{
    protected static ?string $model = EmailTemplate::class;

    // --- CONFIGURAÇÃO DA NAVEGAÇÃO ---
    protected static ?string $navigationIcon = 'heroicon-o-envelope-open'; // Ícone melhorado
    protected static ?string $navigationLabel = 'Modelos de E-mail';
    protected static ?string $pluralModelLabel = 'Modelos de E-mail';
    
    // AJUSTE ADICIONADO: Move para o grupo "Sistema"
    protected static ?string $navigationGroup = 'Sistema';
    protected static ?int $navigationSort = 106; // Ordem dentro do grupo

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Grid::make(2)
                    ->schema([
                        TextInput::make('titulo')
                            ->label('Título Interno (para sua referência)')
                            ->required()
                            ->maxLength(100),

                        TextInput::make('assunto')
                            ->label('Assunto do E-mail (o que o usuário vê)')
                            ->required()
                            ->maxLength(150),
                    ]),

                Textarea::make('mensagem')
                    ->label('Corpo do E-mail / Template')
                    ->required()
                    ->rows(10)
                    ->placeholder('Exemplo: Olá {{ nome }}, sua assinatura do plano {{ plano }} vence em {{ vencimento }}.')
                    ->helperText('Variáveis disponíveis: {{ nome }}, {{ plano }}, {{ vencimento }}'),

                // Este componente de pré-visualização é uma excelente ideia!
                Placeholder::make('preview')
                    ->label('Pré-visualização (Exemplo)')
                    ->content(fn ($get) => new HtmlString(
                        // Supondo que você tenha uma view para o preview
                        // Este código está funcional, desde que a view 'emails.preview' exista
                        view('emails.preview', [
                            'nome' => 'Usuário de Teste',
                            'plano' => 'Premium',
                            'vencimento' => now()->addDays(7)->format('d/m/Y'),
                            'mensagem' => $get('mensagem'),
                        ])->render()
                    ))
                    ->columnSpanFull()
                    ->visible(fn ($get) => filled($get('mensagem'))),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                TextColumn::make('titulo')
                    ->label('Título Interno')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('assunto')
                    ->label('Assunto do E-mail')
                    ->limit(60)
                    ->wrap(),

                TextColumn::make('updated_at')
                    ->label('Última Modificação')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([])
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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmailTemplates::route('/'),
            'create' => Pages\CreateEmailTemplate::route('/create'),
            'edit' => Pages\EditEmailTemplate::route('/{record}/edit'),
        ];
    }
}