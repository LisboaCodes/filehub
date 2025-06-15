<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\Plano;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn; // Importado para o novo visual
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Usuários';
    protected static ?string $pluralModelLabel = 'Usuários';
    protected static ?string $modelLabel = 'Usuário';

    // ADICIONADO para mover ao grupo correto
    protected static ?string $navigationGroup = 'Gestão de Usuários';

    public static function form(Form $form): Form
    {
        // Seu formulário original, mantido 100% como você enviou, com uma correção.
        return $form->schema([
            TextInput::make('name')
                ->label('Nome')
                ->required(),

            TextInput::make('email')
                ->label('E-mail')
                ->email()
                ->required()
                ->unique(ignoreRecord: true),

            TextInput::make('password')
                ->label('Senha')
                ->password()
                ->dehydrateStateUsing(fn ($state) => filled($state) ? Hash::make($state) : null)
                ->dehydrated(fn ($state) => filled($state))
                ->required(fn (string $context) => $context === 'create'),

            Select::make('nivel_acesso')
                ->label('Nível de Acesso')
                ->options([
                    'admin' => 'Administrador',
                    'moderador' => 'Moderador',
                    'colaborador' => 'Colaborador',
                    'usuario' => 'Usuário',
                ])
                ->default('usuario')
                ->required(),

            Select::make('status')
                ->label('Status')
                ->options([
                    'ativo' => 'Ativo',
                    'desativado' => 'Desativado',
                    'banido' => 'Banido',
                    'inadimplente' => 'Inadimplente',
                    'trial' => 'Trial',
                ])
                ->default('ativo')
                ->required(),

            // Correção essencial: O campo deve ser 'plano_id' e usar ->relationship()
            Select::make('plano_id')
                ->label('Plano')
                ->relationship('plano', 'nome')
                ->searchable()
                ->nullable(),

            DatePicker::make('assinatura')->label('Data de Assinatura')->nullable(),
            DatePicker::make('data_expiracao')->label('Expira em')->nullable(),
            DatePicker::make('data_criacao')->label('Data de Criação')->nullable(),

            TextInput::make('security_pin')
                ->label('PIN de Segurança')
                ->length(4)
                ->numeric()
                ->nullable(),

            TextInput::make('whatsapp')
                ->label('WhatsApp')
                ->tel()
                ->prefix('+55')
                ->mask('(99) 99999-9999')
                ->nullable(),

            TextInput::make('id_telegram')
                ->label('ID do Telegram')
                ->nullable(),

            TextInput::make('id_filehub')
                ->label('ID FileHub')
                ->disabled()
                ->dehydrated(false)
                ->helperText('Gerado automaticamente'),

            FileUpload::make('avatar')
                ->label('Avatar')
                ->image()
                ->directory('avatars')
                ->preserveFilenames()
                ->maxSize(2048)
                ->nullable(),

            Textarea::make('google_access_token')
                ->label('Google Access Token')
                ->nullable(),

            TextInput::make('google_id')
                ->label('Google ID')
                ->nullable(),

            TextInput::make('invite_link')
                ->label('Link de Convite')
                ->nullable(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Nome')->searchable(),
                TextColumn::make('email')->label('E-mail')->searchable(),
                // Usando Badge para um visual melhor
                BadgeColumn::make('nivel_acesso')->label('Acesso'),
                BadgeColumn::make('status')->label('Status'),
                // Usa o relacionamento para exibir o nome do plano
                TextColumn::make('plano.nome')->label('Plano')->placeholder('N/A'),

                // --- IMPLEMENTAÇÃO ADICIONADA ---
                BadgeColumn::make('status_online')
                    ->label('Atividade')
                    ->getStateUsing(fn (User $record): bool => $record->isOnline())
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Online' : 'Offline')
                    ->color(fn (bool $state): string => $state ? 'success' : 'danger'),
                // --- FIM DA IMPLEMENTAÇÃO ---

                TextColumn::make('data_expiracao')->label('Expira em')->date('d/m/Y'),
            ])
            // --- IMPLEMENTAÇÃO ADICIONADA ---
            ->poll('10s') // Atualiza a tabela a cada 10s
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
    
    // --- IMPLEMENTAÇÃO ADICIONADA ---
    public static function getRelations(): array
    {
        return [
            RelationManagers\CotasRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
    
    // Seus hooks originais, mantidos como você pediu.
    public static function beforeCreate(array $data): array
    {
        $data['id_filehub'] = uniqid('fh_', true);
        return $data;
    }

    public static function beforeSave(array $data): array
    {
        if (! isset($data['id_filehub'])) {
            $data['id_filehub'] = uniqid('fh_', true);
        }

        return $data;
    }
}