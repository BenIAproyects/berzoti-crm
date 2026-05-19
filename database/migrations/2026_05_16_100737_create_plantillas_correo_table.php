<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plantillas_correo', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('asunto');
            $table->longText('cuerpo_html');
            $table->boolean('activo')->default(true);
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();

            $table->index('activo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plantillas_correo');
    }
};
