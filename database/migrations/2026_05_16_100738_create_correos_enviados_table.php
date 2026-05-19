<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('correos_enviados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes');
            $table->foreignId('campana_id')->nullable()->constrained('campanas')->nullOnDelete();
            $table->foreignId('plantilla_id')->nullable()->constrained('plantillas_correo')->nullOnDelete();
            $table->foreignId('usuario_id')->constrained('users');
            $table->string('destinatario');
            $table->string('asunto');
            $table->longText('cuerpo_renderizado');
            $table->string('estado_envio', 20)->default('pendiente'); // pendiente|enviado|fallido
            $table->text('error_mensaje')->nullable();
            $table->timestamp('enviado_en')->nullable();
            $table->timestamps();

            $table->index(['cliente_id', 'estado_envio']);
            $table->index('campana_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('correos_enviados');
    }
};
