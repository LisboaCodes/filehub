<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CotaResource\Pages;
use App\Models\Cota;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set; // <-- Importante para o formulário reativo
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

// Imports organizados
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;

class CotaResource extends Resource
{
    protected static ?string $model = Cota::class;
    
    // --- NAVEGAÇÃO ---
    protected static ?string $navigationIcon = 'heroicon-o-ticket';
    protected static ?string $modelLabel = 'Cota de Usuário';
    protected static ?string $pluralModelLabel = 'Cotas de Usuários';
    protected static ?string $navigationGroup = 'Gestão de Usuários';
    protected static ?int $navigationSort = 2; // Ordem para aparecer depois de "Planos"

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('user_id')
                    ->label('Usuário')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->required()
                    // 1. Torna o campo reativo
                    ->reactive()
                    // 2. Define a ação a ser executada após a atualização do campo
                    ->afterStateUpdated(function (Set $set, ?string $state) {
                        if ($state) {
                            $user = User::find($state);
                            // 3. Atualiza o campo 'id_telegram' com o valor do usuário selecionado
                            $set('id_telegram', $user?->id_telegram);
                        } else {
                            $set('id_telegram', null);
                        }
                    }),
                
                // 4. Campo agora é desabilitado para o usuário, pois é preenchido automaticamente
                TextInput::make('id_telegram')
                    ->label('ID Telegram')
                    ->numeric()
                    ->disabled()
                    ->dehydrated(),

                TextInput::make('plataforma')->required(),
                TextInput::make('bot')->required(),
                TextInput::make('total_cotas')->label('Cotas Atuais')->numeric()->required()->default(0),
                TextInput::make('cota_original')->label('Cotas Originais')->numeric()->required()->default(0),
                
            ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')->label('Usuário')->searchable()->sortable(),
                TextColumn::make('plataforma')->searchable(),
                TextColumn::make('bot')->searchable(),
                TextColumn::make('total_cotas')->label('Cotas Atuais')->sortable(),
                TextColumn::make('cota_original')->label('Cotas Originais'),
                TextColumn::make('updated_at')->label('Última Atualização')->dateTime('d/m/Y H:i')->sortable(),
            ])
            ->filters([
                SelectFilter::make('user')->label('Usuário')->relationship('user', 'name'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Action::make('resetarCota')
                    ->label('Resetar')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->action(fn (Cota $record) => $record->update(['total_cotas' => $record->cota_original])),
            ])
            ->bulkActions([
                BulkAction::make('alterarCotas')
                    ->label('Alterar Cotas em Lote')
                    ->icon('heroicon-o-arrows-up-down')
                    ->form([
                        TextInput::make('quantidade')
                            ->label('Quantidade para adicionar ou remover')
                            ->helperText('Use números positivos para adicionar (ex: 10) e negativos para remover (ex: -5).')
                            ->numeric()
                            ->required(),
                    ])
                    ->action(function (Collection $records, array $data): void {
                        foreach ($records as $record) {
                            $record->increment('total_cotas', (int) $data['quantidade']);
                        }
                    }),
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
    
    public static function getRelations(): array { return []; }
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCotas::route('/'),
            'create' => Pages\CreateCota::route('/create'),
            'edit' => Pages\EditCota::route('/{record}/edit'),
        ];
    }
}