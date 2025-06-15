<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->date('assinatura')->nullable()->after('updated_at');
            $table->date('data_expiracao')->nullable()->after('assinatura');
            $table->date('data_criacao')->nullable()->after('data_expiracao');

            $table->string('security_pin', 4)->nullable()->after('data_criacao');
            $table->string('whatsapp')->nullable()->after('security_pin');

            $table->enum('nivel_acesso', ['admin', 'moderador', 'colaborador', 'usuario'])->default('usuario')->after('whatsapp');

            $table->string('id_telegram')->nullable()->after('nivel_acesso');
            $table->string('id_filehub')->unique()->nullable()->after('id_telegram');

            $table->enum('plano', ['1','2','3','4','5','6','7','8','9'])->nullable()->after('id_filehub');

            $table->text('google_access_token')->nullable()->after('plano');
            $table->string('google_id')->nullable()->after('google_access_token');
            $table->string('invite_link')->nullable()->after('google_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'assinatura',
                'data_expiracao',
                'data_criacao',
                'security_pin',
                'whatsapp',
                'nivel_acesso',
                'id_telegram',
                'id_filehub',
                'plano',
                'google_access_token',
                'google_id',
                'invite_link',
            ]);
        });
    }
};
