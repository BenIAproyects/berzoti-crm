<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('importaciones_clientes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('users');
            $table->string('archivo');
            $table->unsignedInteger('total_filas')->default(0);
            $table->unsignedInteger('total_importadas')->default(0);
            $table->unsignedInteger('total_duplicadas')->default(0);
            $table->unsignedInteger('total_error')->default(0);
            $table->string('estado', 20)->default('pendiente'); // pendiente, procesando, completado, con_errores
            $table->json('resultado_json')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('importaciones_clientes');
    }
};
