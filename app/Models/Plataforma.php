<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plataforma extends Model
{
    use HasFactory;

    /**
     * AQUI ESTÁ A CORREÇÃO:
     * A propriedade $fillable define quais campos da tabela podem ser 
     * preenchidos em massa, como ao usar o formulário do Filament.
     */
    protected $fillable = [
        'nome',
        'bot_identifier',
        'status',
    ];
}