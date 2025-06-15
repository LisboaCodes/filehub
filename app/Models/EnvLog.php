<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EnvLog extends Model
{
    // permite que esses campos sejam atribuídos em massa
    protected $fillable = [
        'user_id',
        'before',
        'after',
    ];

    // faz o cast de before/after para array automaticamente
    protected $casts = [
        'before' => 'array',
        'after'  => 'array',
    ];
}
