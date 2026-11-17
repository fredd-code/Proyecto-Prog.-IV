<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabla de Descuentos y Abonos Extras (Préstamos, Anticipos, Horas Extras, Sanciones)
     */
    public function up(): void
    {
        Schema::create('ajustes_salariales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->cascadeOnDelete();
            $table->foreignId('trabajador_id')->constrained('trabajadores')->cascadeOnDelete();
            $table->enum('tipo', ['deduccion', 'asignacion']); // Deducción (Préstamo) o Asignación (Bono extra)
            $table->string('concepto', 150);
            $table->decimal('monto_total', 10, 2);
            $table->decimal('monto_cuota', 10, 2);
            $table->integer('cuotas_totales')->default(1);
            $table->integer('cuotas_cobradas')->default(0);
            $table->boolean('reflejar_en_recibo')->default(true); // Decisión del patrono
            $table->date('fecha_inicio');
            $table->enum('estado', ['activo', 'completado', 'cancelado'])->default('activo');
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ajustes_salariales');
    }
};
