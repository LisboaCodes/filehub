<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up(): void
{
    Schema::create('plataformas', function (Blueprint $table) {
        $table->id();
        $table->string('nome')->unique(); // Ex: Freepik
        $table->string('bot_identifier')->unique(); // Ex: bot_freepik
        $table->string('status')->default('Online'); // Opções: Online, Manutenção, Desativado
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plataformas');
    }
};
