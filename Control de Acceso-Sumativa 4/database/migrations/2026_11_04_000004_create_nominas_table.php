<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecuta la migración: Cabecera del período de nómina procesado.
     */
    public function up(): void
    {
        Schema::create('nominas', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 50)->unique();
            $table->enum('tipo', ['quincenal', 'semanal']);
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->decimal('total_asignaciones', 14, 2)->default(0.00);
            $table->decimal('total_deducciones', 14, 2)->default(0.00);
            $table->decimal('total_neto', 14, 2)->default(0.00);
            $table->enum('estado', ['procesada', 'pagada'])->default('procesada');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nominas');
    }
};
