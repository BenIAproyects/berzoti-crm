<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guias_remision', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->unique();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->foreignId('orden_compra_id')->nullable()->constrained('ordenes_compra')->nullOnDelete();
            $table->foreignId('factura_id')->nullable()->constrained('facturas')->nullOnDelete();
            $table->foreignId('vendedor_id')->constrained('users')->restrictOnDelete();
            $table->string('numero_guia', 50)->nullable();
            $table->date('fecha_emision');
            $table->date('fecha_entrega')->nullable();
            $table->string('estado_entrega', 30)->default('pendiente');
            $table->string('direccion_entrega', 500)->nullable();
            $table->text('observaciones')->nullable();
            $table->string('archivo_adjunto')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guias_remision');
    }
};
