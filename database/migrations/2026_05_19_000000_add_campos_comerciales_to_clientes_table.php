<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->unsignedInteger('cantidad_compra')->nullable()->after('prioridad');
            $table->string('mes_contacto', 20)->nullable()->after('cantidad_compra');
            $table->decimal('precio_ano_anterior', 10, 2)->nullable()->after('mes_contacto');
        });
    }

    public function down(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->dropColumn(['cantidad_compra', 'mes_contacto', 'precio_ano_anterior']);
        });
    }
};
