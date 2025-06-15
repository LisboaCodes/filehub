<?php

namespace App\Filament\Pages;

use App\Models\Notificacao;
use App\Models\User;
use App\Notifications\NotificacaoDiretaNotification;
use Filament\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification as Toast;
use Filament\Pages\Page;

class EnviarNotificacao extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    protected static string $view = 'filament.pages.enviar-notificacao';
    protected static ?string $navigationGroup = 'Gestão de Usuários';
    protected static ?string $navigationLabel = 'Enviar Notificação';
    protected static ?string $title = 'Enviar Notificação Direta';
    protected static ?int $navigationSort = 4;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('recipients')
                    ->label('Destinatários')
                    ->options(User::pluck('name', 'id'))
                    ->multiple()
                    ->searchable()
                    ->helperText('Deixe em branco para enviar para TODOS os usuários.'),

                Select::make('quick_template')
                    ->label('Modelos Rápidos')
                    ->reactive()
                    ->afterStateUpdated(fn (Set $set, ?string $state) => $this->applyTemplate($set, $state))
                    ->options([
                        'free_form' => 'Livre (Personalizar Título e Conteúdo)',
                        'payment_confirmed' => '✅ Pagamento Confirmado',
                        'subscription_due_soon' => '⚠️ Vencimento Próximo',
                        'subscription_overdue' => '❌ Conta Vencida',
                    ]),
                
                TextInput::make('message_title')
                    ->label('Título da Notificação/Email')
                    ->hidden(fn (Get $get) => $get('quick_template') !== 'free_form' && !empty($get('quick_template')))
                    ->required(fn (Get $get) => $get('quick_template') === 'free_form'),

                RichEditor::make('message_content')
                    ->label('Conteúdo da Mensagem')
                    ->required(),

                FileUpload::make('attachment')
                    ->label('Anexo (Opcional)')
                    ->disk('public')->directory('attachments')->maxSize(10240)
                    ->acceptedFileTypes(['image/png', 'image/jpeg', 'application/pdf']),

                Checkbox::make('send_email')
                    ->label('Enviar também por E-mail (Requer fila configurada)'),
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [Action::make('send')->label('Enviar Notificação')->submit('send')];
    }

    public function send(): void
    {
        $data = $this->form->getState();
        $recipients = empty($data['recipients']) ? User::all() : User::whereIn('id', $data['recipients'])->get();
        $attachmentPath = is_array($data['attachment']) ? ($data['attachment'][0] ?? null) : $data['attachment'];

        // --- LÓGICA DE TÍTULO CORRIGIDA E FINAL ---
        $title = ''; 

        // Se for um formulário livre (ou nenhum template foi escolhido), pega o título do campo
        if (empty($data['quick_template']) || $data['quick_template'] === 'free_form') {
            $title = $data['message_title'];
        } else {
            // Se for um template, busca o título na nossa lista de templates interna
            $templates = [
                'payment_confirmed' => 'Confirmação de Pagamento - FileHub',
                'subscription_due_soon' => 'Lembrete: Vencimento do Seu Plano',
                'subscription_overdue' => 'Aviso: Sua Conta Está Vencida',
            ];
            $title = $templates[$data['quick_template']] ?? 'Notificação do Sistema';
        }
        // --- FIM DA CORREÇÃO ---

        $notificacao = Notificacao::create([
            'sender_id' => auth()->id(),
            'title' => $title,
            'content' => $data['message_content'],
            'attachment_path' => $attachmentPath,
        ]);

        $notificacao->recipients()->attach($recipients->pluck('id'));

        foreach ($recipients as $recipient) {
            // Notificação no sistema (o "sininho" do Filament)
            $recipient->notify(
                Toast::make()->title($title)->body('Você tem uma nova notificação.')->toDatabase()
            );
            
            // Se marcado, dispara a notificação por e-mail para a fila
            if ($data['send_email']) {
                $recipient->notify(new NotificacaoDiretaNotification($notificacao));
            }
        }

        Toast::make()->title('Notificações enviadas com sucesso!')->success()->send();
        $this->form->fill(); // Limpa o formulário
    }

    public function applyTemplate(Set $set, ?string $state): void
    {
        // Conteúdo dos templates
        $templates = [
            'payment_confirmed' => ['title' => 'Confirmação de Pagamento - FileHub', 'content' => '<p>Confirmamos o recebimento do seu pagamento com sucesso! 🎉</p><p>Sua conta foi atualizada e você já pode continuar aproveitando todos os benefícios da plataforma.</p><p>Abraços,<br>Equipe FileHub 💜</p>'],
            'subscription_due_soon' => ['title' => 'Lembrete: Vencimento do Seu Plano', 'content' => '<p>Seu plano está se aproximando do vencimento. Renove sua assinatura para continuar aproveitando todos os recursos sem interrupções!</p><p>Não perca tempo!<br>Equipe FileHub</p>'],
            'subscription_overdue' => ['title' => 'Aviso: Sua Conta Está Vencida', 'content' => '<p>Informamos que sua conta está vencida. Seu acesso à plataforma está desativado até a renovação.</p><p>Para reativar sua conta, por favor, renove seu plano.</p><p>Agradecemos a compreensão.<br>Equipe FileHub</p>'],
        ];

        if ($state && isset($templates[$state])) {
            $set('message_title', $templates[$state]['title']);
            $set('message_content', $templates[$state]['content']);
        }
    }
}