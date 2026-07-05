<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->string('fuente', 50)->nullable()->after('activo');
            $table->string('zona', 50)->nullable()->after('fuente');
            $table->string('segmento', 50)->nullable()->after('zona');
        });
    }

    public function down(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->dropColumn(['fuente', 'zona', 'segmento']);
        });
    }
};
