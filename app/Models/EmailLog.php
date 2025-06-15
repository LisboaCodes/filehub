<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailLog extends Model
{
    protected $table = 'email_logs';

    protected $fillable = [
        'user_id',
        'assunto',
        'mensagem',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
