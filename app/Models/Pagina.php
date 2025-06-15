<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pagina extends Model
{
    use HasFactory;

    // Campos que podem ser preenchidos em massa
    protected $fillable = [
        'title',
        'slug',
        'content',
        'is_published',
    ];

    // Converte o valor de 'is_published' para booleano (true/false)
    protected $casts = [
        'is_published' => 'boolean',
    ];
}