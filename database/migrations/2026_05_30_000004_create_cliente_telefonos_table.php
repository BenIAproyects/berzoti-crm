<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cliente_telefonos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->string('numero', 30);
            $table->string('tipo')->default('celular');
            $table->boolean('es_principal')->default(false);
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->index('cliente_id');
            $table->unique(['cliente_id', 'numero']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cliente_telefonos');
    }
};
