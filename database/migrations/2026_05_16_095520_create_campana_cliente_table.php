<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campana_cliente', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campana_id')->constrained('campanas')->cascadeOnDelete();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->string('estado_en_campana', 50)->default('nuevo');
            $table->timestamps();

            $table->unique(['campana_id', 'cliente_id']);
            $table->index('estado_en_campana');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campana_cliente');
    }
};
