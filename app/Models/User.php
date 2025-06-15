<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'nivel_acesso', 'status',
        'plano_id', // Chave estrangeira para o plano
        'assinatura', 'data_expiracao', 'data_criacao', 'security_pin',
        'whatsapp', 'id_telegram', 'id_filehub', 'avatar',
        'google_access_token', 'google_id', 'invite_link',
        'last_seen_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'assinatura'        => 'date',
        'data_expiracao'    => 'date',
        'data_criacao'      => 'date',
        'password'          => 'hashed',
        'last_seen_at'      => 'datetime',
    ];

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    // --- RELACIONAMENTOS ---
    public function cotas()
    {
        return $this->hasMany(Cota::class);
    }

    public function plano()
    {
        return $this->belongsTo(Plano::class);
    }

    public function notificacoes()
    {
        return $this->belongsToMany(Notificacao::class, 'notificacao_user')->withPivot('read_at');
    }

    // --- MÉTODOS DE LÓGICA ---
    public function isOnline(): bool
    {
        return $this->last_seen_at && $this->last_seen_at->gt(now()->subMinutes(5));
    }
}