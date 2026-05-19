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
        Schema::create('seguimientos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->foreignId('campana_id')->nullable()->constrained('campanas')->nullOnDelete();
            $table->foreignId('usuario_id')->constrained('users')->cascadeOnDelete();
            $table->string('tipo', 30);
            $table->dateTime('fecha_hora');
            $table->text('detalle');
            $table->text('resultado')->nullable();
            $table->string('estado_comercial_nuevo', 50)->nullable();
            $table->string('proxima_accion', 255)->nullable();
            $table->date('fecha_proxima_accion')->nullable();
            $table->timestamps();

            $table->index('cliente_id');
            $table->index('usuario_id');
            $table->index('fecha_hora');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seguimientos');
    }
};
