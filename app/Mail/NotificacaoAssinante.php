<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotificacaoAssinante extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $mensagem,
        public string $nome,
        public string $assunto = 'Notificação da Plataforma'
    ) {}

    public function build(): self
    {
        return $this->subject($this->assunto)
            ->view('emails.notificacao-assinante')
            ->with([
                'mensagem' => $this->mensagem,
                'nome' => $this->nome,
            ]);
    }
}
