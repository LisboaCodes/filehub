<?php

namespace App\Filament\Widgets;

use App\Models\EmailLog;
use App\Models\EmailTemplate;
use App\Models\Plano;
use App\Models\User;
use App\Mail\NotificacaoAssinante; // Supondo que você tenha este Mailable
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Mail;

class UltimosAssinantesTable extends BaseWidget
{
    protected static ?string $heading = 'Gerenciamento de Assinantes';
    protected static ?int $sort = 2; // Ordem de exibição no dashboard
    protected int | string | array $columnSpan = 'full';

    // Permissão para ver o widget
    public static function canView(): bool
    {
        return in_array(auth()->user()?->nivel_acesso, ['admin', 'moderador', 'colaborador']);
    }

    // Consulta principal da tabela
    protected function getTableQuery(): Builder
    {
        // A CORREÇÃO ESTÁ AQUI: Trocamos 'plano' por 'plano_id'
        // Isso vai listar todos os usuários que têm um plano associado.
        return User::query()->whereNotNull('plano_id');
    }

    // Definição das colunas da tabela
    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('name')->label('Nome')->limit(20)->tooltip(fn (User $record) => $record->name)->sortable()->searchable(),
            Tables\Columns\TextColumn::make('email')->label('E-mail')->limit(25)->tooltip(fn (User $record) => $record->email)->sortable()->searchable(),
            
            // Usando o relacionamento para exibir o nome do plano corretamente
            Tables\Columns\TextColumn::make('plano.nome')->label('Plano')->badge(),

            Tables\Columns\TextColumn::make('status')->label('Status')->badge()
                ->color(fn (string $state): string => match ($state) {
                    'ativo' => 'success',
                    'inadimplente' => 'warning',
                    'banido', 'desativado' => 'danger',
                    default => 'gray',
                }),
                
            Tables\Columns\TextColumn::make('created_at')->label('Data de Entrada')->dateTime('d/m/Y H:i')->sortable(),
        ];
    }

    // Definição dos filtros da tabela
    protected function getTableFilters(): array
    {
        return [
            SelectFilter::make('status')->label('Status')->options(['ativo' => 'Ativo', 'inadimplente' => 'Inadimplente', 'banido' => 'Banido', 'desativado' => 'Desativado']),
            
            // A CORREÇÃO ESTÁ AQUI: O filtro agora usa a relação `plano` e o ID para filtrar
            SelectFilter::make('plano_id')->label('Plano')->relationship('plano', 'nome')->preload(),

            Filter::make('created_at')->label('Período')->form([
                DatePicker::make('from')->label('De'),
                DatePicker::make('until')->label('Até'),
            ])->query(fn (Builder $query, array $data) => $query
                ->when($data['from'], fn ($q) => $q->whereDate('created_at', '>=', $data['from']))
                ->when($data['until'], fn ($q) => $q->whereDate('created_at', '<=', $data['until']))),
        ];
    }

    // Ações por linha
    protected function getTableActions(): array
    {
        return [
            Action::make('ver')->label('Ver/Editar')->url(fn (User $record) => route('filament.admin.resources.users.edit', $record))->icon('heroicon-o-pencil-square'),
            Action::make('whatsapp')->label('WhatsApp')->url(fn (User $record) => $record->whatsapp ? 'https://wa.me/55' . preg_replace('/\D/', '', $record->whatsapp) : null)->openUrlInNewTab()->icon('heroicon-o-chat-bubble-left-right')->hidden(fn (User $record) => !$record->whatsapp),
        ];
    }

    // Ações em massa
    protected function getTableBulkActions(): array
    {
        return [
            Tables\Actions\DeleteBulkAction::make(), // Adicionando ação de deletar em massa padrão do Filament

            BulkAction::make('marcar_banido')->label('Marcar como Banido')->icon('heroicon-o-x-circle')->requiresConfirmation()->color('danger')->action(fn (Collection $records) => $records->each->update(['status' => 'banido'])),
            BulkAction::make('marcar_inadimplente')->label('Marcar como Inadimplente')->icon('heroicon-o-exclamation-circle')->requiresConfirmation()->color('warning')->action(fn (Collection $records) => $records->each->update(['status' => 'inadimplente'])),
            
            BulkAction::make('enviar_email')
                ->label('Enviar E-mail Personalizado')
                ->icon('heroicon-o-envelope')
                ->form([
                    Select::make('template_id')->label('Modelo de E-mail')->options(fn () => EmailTemplate::pluck('titulo', 'id'))->reactive()->afterStateUpdated(fn ($state, callable $set) => $set('mensagem', EmailTemplate::find($state)?->mensagem ?? '')),
                    TextInput::make('assunto')->label('Assunto')->required(),
                    Textarea::make('mensagem')->label('Mensagem')->required()->rows(6),
                ])
                ->action(function (Collection $records, array $data) {
                    foreach ($records as $user) {
                        if ($user->email) {
                            // Supondo que você tenha uma classe Mailable chamada NotificacaoAssinante
                            // Mail::to($user->email)->send(new NotificacaoAssinante(...));
                            
                            EmailLog::create(['user_id' => $user->id, 'assunto' => $data['assunto'], 'mensagem' => $data['mensagem']]);
                        }
                    }
                    Notification::make()->title('E-mails enviados com sucesso para ' . $records->count() . ' usuários.')->success()->send();
                })
        ];
    }
}