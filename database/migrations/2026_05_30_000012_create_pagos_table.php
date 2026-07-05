<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('factura_id')->constrained('facturas')->cascadeOnDelete();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->date('fecha_pago');
            $table->decimal('monto_pagado', 12, 2);
            $table->string('metodo_pago', 30)->default('transferencia');
            $table->string('banco', 100)->nullable();
            $table->string('numero_operacion', 100)->nullable();
            $table->text('observaciones')->nullable();
            $table->string('archivo_adjunto')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};
