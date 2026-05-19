<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('correos_enviados', function (Blueprint $table) {
            $table->string('mensaje_id')->nullable()->after('estado_envio');
            $table->boolean('abierto')->default(false)->after('mensaje_id');
            $table->timestamp('abierto_en')->nullable()->after('abierto');
            $table->unsignedInteger('veces_abierto')->default(0)->after('abierto_en');
        });
    }

    public function down(): void
    {
        Schema::table('correos_enviados', function (Blueprint $table) {
            $table->dropColumn(['mensaje_id', 'abierto', 'abierto_en', 'veces_abierto']);
        });
    }
};
