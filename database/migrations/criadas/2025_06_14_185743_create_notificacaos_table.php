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
    Schema::create('notificacoes', function (Blueprint $table) {
        $table->id();
        $table->foreignId('sender_id')->nullable()->constrained('users')->nullOnDelete();
        $table->string('title');
        $table->text('content');
        $table->string('attachment_path')->nullable();
        $table->timestamps();
    });

    Schema::create('notificacao_user', function (Blueprint $table) {
        // A CORREÇÃO ESTÁ AQUI: ->constrained('notificacoes')
        $table->foreignId('notificacao_id')->constrained('notificacoes')->onDelete('cascade');
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        $table->timestamp('read_at')->nullable();
        $table->primary(['notificacao_id', 'user_id']);
    });
}
};
