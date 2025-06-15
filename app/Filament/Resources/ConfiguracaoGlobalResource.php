<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ConfiguracaoGlobalResource\Pages;
use App\Models\ConfiguracaoGlobal;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;

// Componentes do Formulário
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Group;

// Componentes da Tabela
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\EditAction; // import para a ação de edição

class ConfiguracaoGlobalResource extends Resource
{
    protected static ?string $model = ConfiguracaoGlobal::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationLabel = 'Configurações Globais';
    protected static ?string $modelLabel = 'Configuração Global';
    protected static ?string $pluralModelLabel = 'Configurações Globais';
    protected static ?string $navigationGroup = 'Sistema';
    protected static ?int $navigationSort = 100;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()->schema([
                    Section::make('Informações Principais')
                        ->schema([
                            TextInput::make('nome_site')
                                ->label('Nome do Site')
                                ->required(),
                            Textarea::make('info_footer')
                                ->label('Informações do Rodapé')
                                ->rows(5)
                                ->nullable(),
                        ]),
                    Section::make('Logos')
                        ->schema([
                            FileUpload::make('logo_navbar')
                                ->label('Logo da Navbar')
                                ->image()
                                ->disk('public')
                                ->directory('logos')
                                ->imagePreviewHeight(100),
                            FileUpload::make('logo_footer')
                                ->label('Logo do Rodapé')
                                ->image()
                                ->disk('public')
                                ->directory('logos')
                                ->imagePreviewHeight(100),
                        ])->columns(2),
                ])->columnSpan(['lg' => 2]),

                Group::make()->schema([
                    Section::make('Configurações Avançadas')
                        ->schema([
                            Toggle::make('modo_manutencao')
                                ->label('Modo de Manutenção')
                                ->helperText('Ative para bloquear o acesso ao site para usuários comuns.'),
                        ])
                ])->columnSpan(['lg' => 1]),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nome_site')->label('Nome do Site'),
                IconColumn::make('modo_manutencao')->boolean()->label('Manutenção Ativa'),
                TextColumn::make('updated_at')->label('Última Modificação')->dateTime('d/m/Y H:i'),
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListConfiguracaoGlobals::route('/'),
            'edit'  => Pages\EditConfiguracaoGlobal::route('/{record}/edit'),
        ];
    }
}
