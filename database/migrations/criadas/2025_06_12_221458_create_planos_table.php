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
    Schema::create('planos', function (Blueprint $table) {
        $table->id();
        $table->string('nome');
        $table->string('preco')->nullable();
        $table->integer('codigo')->unique(); // ex: 1, 2, 3, ..., 10
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('planos');
    }
};
