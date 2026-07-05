<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->boolean('requiere_validacion')->default(false)->after('activo');
            $table->string('cantidad_original_excel')->nullable()->after('requiere_validacion')
                  ->comment('Valor original del Excel cuando fue marcado como sospechoso');
        });
    }

    public function down(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->dropColumn(['requiere_validacion', 'cantidad_original_excel']);
        });
    }
};
