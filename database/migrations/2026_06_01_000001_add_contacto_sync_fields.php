<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cliente_correos', function (Blueprint $table) {
            $table->string('nombre')->nullable()->after('email');
            $table->foreignId('contacto_id')->nullable()->after('activo')
                  ->constrained('cliente_contactos')->nullOnDelete();
        });

        Schema::table('cliente_telefonos', function (Blueprint $table) {
            $table->foreignId('contacto_id')->nullable()->after('activo')
                  ->constrained('cliente_contactos')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('cliente_correos', function (Blueprint $table) {
            $table->dropForeign(['contacto_id']);
            $table->dropColumn(['nombre', 'contacto_id']);
        });

        Schema::table('cliente_telefonos', function (Blueprint $table) {
            $table->dropForeign(['contacto_id']);
            $table->dropColumn('contacto_id');
        });
    }
};
