<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guia_remision_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guia_remision_id')->constrained('guias_remision')->cascadeOnDelete();
            $table->string('producto', 255);
            $table->text('descripcion')->nullable();
            $table->integer('cantidad_enviada');
            $table->text('observaciones')->nullable();
            $table->integer('orden')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guia_remision_items');
    }
};
