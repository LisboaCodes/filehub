<?php

namespace App\Notifications;

use App\Models\Notificacao;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

// ShouldQueue faz com que o envio de e-mails vá para a fila, sem travar a tela do admin
class NotificacaoDiretaNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Notificacao $notificacao)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail']; // Define que esta notificação será enviada por e-mail
    }

    public function toMail(object $notifiable): MailMessage
    {
        // Monta a mensagem de e-mail
        $mailMessage = (new MailMessage)
            ->subject($this->notificacao->title) // Assunto do e-mail
            ->greeting('Olá, ' . $notifiable->name . '!') // Saudação personalizada
            ->line('Você recebeu uma nova mensagem da nossa equipe:')
            // A função HtmlString garante que o conteúdo do RichEditor seja renderizado como HTML no e-mail
            ->line(new \Illuminate\Support\HtmlString($this->notificacao->content));
        
        // Se houver um anexo na notificação, anexa ao e-mail
        if ($this->notificacao->attachment_path) {
            $mailMessage->attach(storage_path('app/public/' . $this->notificacao->attachment_path));
        }
        
        $mailMessage->line('Agradecemos a sua atenção.');
        
        return $mailMessage;
    }
}