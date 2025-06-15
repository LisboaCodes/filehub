<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Notificacao extends Model
{
    use HasFactory;

    /**
     * Força o Eloquent a usar o nome de tabela 'notificacoes' (com 'e'),
     * que é o nome correto que definimos na nossa migration.
     */
    protected $table = 'notificacoes';

    /**
     * Campos que podem ser preenchidos em massa.
     */
    protected $fillable = [
        'sender_id',
        'title',
        'content',
        'attachment_path',
    ];

    /**
     * Define o relacionamento "Muitos para Muitos" com Usuários (os destinatários).
     * Uma notificação pode ser enviada para muitos usuários.
     */
    public function recipients(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'notificacao_user')->withPivot('read_at');
    }

    /**
     * Define o relacionamento "Pertence a" com Usuário (o remetente).
     * Uma notificação foi enviada por um usuário (admin).
     */
public function sender()
{
    return $this->belongsTo(User::class, 'sender_id');
}
}