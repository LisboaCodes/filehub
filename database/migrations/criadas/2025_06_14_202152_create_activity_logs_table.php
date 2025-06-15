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
    Schema::create('activity_logs', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // Quem fez a ação (pode ser nulo)
        $table->string('event_type'); // Ex: 'Login', 'Logout', 'ProfileUpdate'
        $table->text('description'); // Ex: "Usuário Lisboa fez login"
        $table->json('properties')->nullable(); // Para guardar dados extras (IP, User Agent, dados antigos/novos)
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
