<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr; // Importe a classe Arr

class ConfiguracaoGlobal extends Model
{
    use HasFactory;

    protected $table = 'configuracao_globals';
    public $timestamps = false;
    
    protected $fillable = [
        'nome_site',
        'logo_navbar',
        'logo_footer',
        'info_footer',
        'modo_manutencao',
    ];

    /**
     * REMOVEMOS os logos daqui, pois vamos controlá-los manualmente abaixo.
     */
    protected $casts = [
        'modo_manutencao' => 'boolean',
    ];

    // ===================================================================
    // AQUI ESTÁ A MÁGICA - CONTROLE MANUAL PARA O LOGO DA NAVBAR
    // ===================================================================

    /**
     * ACCESSOR: Chamado quando o Laravel LÊ o campo 'logo_navbar' do banco.
     * Transforma o texto salvo (string) em um array, que o Filament entende.
     */
    protected function logoNavbar(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: fn ($value) => is_string($value) ? [$value] : $value,
        );
    }

    /**
     * ACCESSOR: Chamado quando o Laravel LÊ o campo 'logo_footer' do banco.
     * Transforma o texto salvo (string) em um array.
     */
    protected function logoFooter(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: fn ($value) => is_string($value) ? [$value] : $value,
        );
    }
}