<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('status', ['ativo', 'desativado', 'banido', 'inadimplente', 'trial'])
                  ->default('ativo')
                  ->after('nivel_acesso');

            $table->string('avatar')->nullable()->after('id_filehub');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['status', 'avatar']);
        });
    }
};
