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
    Schema::create('mensagens', function (Blueprint $table) {
        $table->id();
        $table->foreignId('sender_id')->nullable()->constrained('users')->nullOnDelete(); // Quem enviou (admin)
        $table->string('title');
        $table->text('content');
        $table->string('attachment_path')->nullable(); // Caminho para o anexo
        $table->timestamps();
    });

    // Tabela pivot para ligar mensagens a muitos usuários (destinatários)
    Schema::create('mensagem_user', function (Blueprint $table) {
        $table->foreignId('mensagem_id')->constrained()->onDelete('cascade');
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->timestamp('read_at')->nullable(); // Para saber se o usuário leu
        $table->primary(['mensagem_id', 'user_id']); // Chave primária composta
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mensagems');
    }
};
