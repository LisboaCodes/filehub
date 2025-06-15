<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cota extends Model
{
    use HasFactory; // Trait para permitir o uso de Factories no futuro, é uma boa prática.

    /**
     * A propriedade $fillable define quais campos da tabela 'cotas'
     * podem ser preenchidos ao usar métodos como Cota::create(). 
     * É uma proteção de segurança do Laravel contra "Mass Assignment".
     */
    protected $fillable = [
        'user_id',
        'id_telegram',
        'plataforma',
        'total_cotas',
        'cota_original',
        'bot',
        'id_filehub',
    ];

    /**
     * Define o relacionamento "Pertence a" (Belongs To).
     * Uma Cota pertence a um Usuário.
     * Isso nos permitirá fazer, por exemplo: $cota->user->name para pegar o nome do usuário.
     */
    public function user()
    {
        // Opcional: se o nome da sua chave estrangeira fosse diferente de 'user_id',
        // você a especificaria aqui. Como usamos o padrão, não é necessário.
        return $this->belongsTo(User::class);
    }
}