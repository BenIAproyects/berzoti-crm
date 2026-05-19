<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->unique();
            $table->string('razon_social');
            $table->string('nombre_comercial')->nullable();
            $table->string('ruc', 20)->nullable()->unique();
            $table->string('tipo_cliente', 50)->default('otro');
            $table->string('sector', 100)->nullable();
            $table->string('contacto_principal');
            $table->string('cargo_contacto', 100)->nullable();
            $table->string('telefono', 30)->nullable();
            $table->string('whatsapp', 30)->nullable();
            $table->string('correo')->nullable();
            $table->string('correo_secundario')->nullable();
            $table->string('pais', 60)->default('Perú');
            $table->string('departamento', 60)->nullable();
            $table->string('provincia', 60)->nullable();
            $table->string('distrito', 60)->nullable();
            $table->string('direccion')->nullable();
            $table->string('referencia')->nullable();
            $table->foreignId('vendedor_asignado_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('estado_comercial', 50)->default('nuevo');
            $table->string('prioridad', 20)->default('media');
            $table->string('origen', 60)->nullable();
            $table->text('observaciones')->nullable();
            $table->date('fecha_ultimo_contacto')->nullable();
            $table->date('fecha_proximo_contacto')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->index('estado_comercial');
            $table->index('tipo_cliente');
            $table->index('vendedor_asignado_id');
            $table->index('activo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
