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
    Schema::create('paginas', function (Blueprint $table) {
        $table->id();
        $table->string('title'); // Título da página (ex: "Sobre Nós")
        $table->string('slug')->unique(); // URL amigável (ex: "sobre-nos")
        $table->longText('content'); // O conteúdo principal da página
        $table->boolean('is_published')->default(false); // Para publicar ou despublicar
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paginas');
    }
};
