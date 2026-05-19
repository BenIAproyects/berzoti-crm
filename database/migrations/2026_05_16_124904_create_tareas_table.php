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
        Schema::create('tareas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->foreignId('campana_id')->nullable()->constrained('campanas')->nullOnDelete();
            $table->foreignId('usuario_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('seguimiento_id')->nullable()->constrained('seguimientos')->nullOnDelete();
            $table->string('titulo', 255);
            $table->text('descripcion')->nullable();
            $table->string('tipo', 50);
            $table->date('fecha_vencimiento');
            $table->string('estado', 20)->default('pendiente');
            $table->string('prioridad', 10)->default('media');
            $table->timestamp('completada_en')->nullable();
            $table->timestamps();

            $table->index('usuario_id');
            $table->index('estado');
            $table->index('fecha_vencimiento');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tareas');
    }
};
