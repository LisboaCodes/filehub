<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('configuracao_globals', function (Blueprint $table) {
            $table->id();
            $table->string('nome_site')->nullable();
            $table->string('logo_navbar')->nullable();
            $table->string('logo_footer')->nullable();
            $table->longText('info_footer')->nullable();
            $table->boolean('modo_manutencao')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('configuracao_globals');
    }
};
