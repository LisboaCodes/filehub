<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlanoResource\Pages;
use App\Models\Cota;
use App\Models\Plano;
use App\Models\Plataforma;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;

class PlanoResource extends Resource
{
    protected static ?string $model = Plano::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationLabel = 'Planos';
    protected static ?string $pluralModelLabel = 'Planos';
    protected static ?string $modelLabel = 'Plano';
    protected static ?string $navigationGroup = 'Gestão de Usuários';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Detalhes do Plano')->schema([
                TextInput::make('nome')->label('Nome do Plano')->required()->columnSpan(1),
                TextInput::make('codigo')->label('Código')->numeric()->required()->unique(ignoreRecord: true)->columnSpan(1),
                TextInput::make('preco')->label('Preço')->prefix('R$')->numeric()->required()->columnSpan(1),
            ])->columns(3),
            Section::make('Bots e Cotas Incluídos no Plano')->schema([
                Repeater::make('configuracao_bots')
                    ->label('')
                    ->schema([
                        Select::make('plataforma')->label('Plataforma')->options(fn() => array_combine(array_keys(config('bots.plataformas', [])), array_keys(config('bots.plataformas', []))))->reactive()->afterStateUpdated(fn(Set $set, ?string $state) => $state ? $set('bot', config('bots.plataformas')[$state]) : null)->required(),
                        TextInput::make('bot')->label('Nome do Bot')->required()->disabled()->dehydrated(),
                        TextInput::make('cotas')->label('Qtd. de Cotas')->numeric()->required()->default(10),
                    ])->columns(3)->defaultItems(1)->addActionLabel('Adicionar Configuração de Bot'),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('codigo')->label('Código')->sortable(),
                TextColumn::make('nome')->label('Nome')->searchable(),
                TextColumn::make('preco')->label('Preço')->money('BRL')->sortable(),
                TextColumn::make('users_count')->counts('users')->label('Usuários'),
            ])
            ->actions([
                EditAction::make(),
                Action::make('atribuirPlano')
                    ->label('Atribuir Plano')
                    ->icon('heroicon-o-user-plus')
                    ->color('success')
                    ->form([
                        // O campo para selecionar múltiplos usuários
                        Select::make('userIds')->label('Selecione um ou mais Usuários')->options(fn() => User::pluck('name', 'id'))->multiple()->searchable()->required(),
                    ])
                    ->action(function (Plano $record, array $data) {
                        $users = User::find($data['userIds']);

                        foreach ($users as $user) {
                            $user->update([
                                'plano_id' => $record->id,
                                'status' => 'ativo',
                                'data_expiracao' => now()->addDays(30)
                            ]);
                            
                            $botsArray = $record->configuracao_bots;

                            if (empty($botsArray)) {
                                continue;
                            }

                            $platformStatuses = Plataforma::pluck('status', 'bot_identifier');
                            Cota::where('user_id', $user->id)->delete();

                            foreach ($botsArray as $config) {
                                if (isset($config['bot']) && $platformStatuses->get($config['bot']) === 'Online') {
                                    Cota::create([
                                        'user_id'       => $user->id,
                                        'id_telegram'   => $user->id_telegram,
                                        'plataforma'    => $config['plataforma'],
                                        'bot'           => $config['bot'],
                                        'total_cotas'   => $config['cotas'],
                                        'cota_original' => $config['cotas'],
                                    ]);
                                }
                            }
                        }
                        Notification::make()->title('Plano atribuído com sucesso!')->success()->send();
                    }),
            ])
            ->bulkActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPlanos::route('/'),
            'create' => Pages\CreatePlano::route('/create'),
            'edit'   => Pages\EditPlano::route('/{record}/edit'),
        ];
    }
}