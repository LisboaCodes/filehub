<?php

namespace App\Filament\Resources\EmailTemplateResource\Pages;

use App\Filament\Resources\EmailTemplateResource;
use App\Mail\NotificacaoAssinante;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Mail;

class EditEmailTemplate extends EditRecord
{
    protected static string $resource = EmailTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),

            Actions\Action::make('enviar_teste')
                ->label('Enviar E-mail de Teste')
                ->icon('heroicon-o-paper-airplane')
                ->requiresConfirmation()
                ->action(function () {
                    Mail::to(auth()->user()->email)->send(
                        new NotificacaoAssinante(
                            mensagem: $this->record->mensagem,
                            nome: auth()->user()->name,
                            assunto: '[TESTE] ' . $this->record->assunto
                        )
                    );
                })
                ->successNotificationTitle('E-mail de teste enviado com sucesso para ' . auth()->user()->email),
        ];
    }
}
