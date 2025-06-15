<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Plano extends Model
{
    use HasFactory;

    protected $fillable = [
        'codigo',
        'nome',
        'preco',
        'configuracao_bots',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    protected function configuracaoBots(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (!is_string($value) || is_null($value)) {
                    return $value;
                }
                $decoded = json_decode($value, true);
                return is_string($decoded) ? json_decode($decoded, true) : $decoded;
            },
            set: fn ($value) => is_array($value) ? json_encode($value) : $value,
        );
    }
}