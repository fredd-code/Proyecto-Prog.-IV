<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecuta la migración: Detalle de liquidación por trabajador con retenciones LOTTT.
     */
    public function up(): void
    {
        Schema::create('nomina_detalles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nomina_id')->constrained('nominas')->onDelete('cascade');
            $table->foreignId('trabajador_id')->constrained('trabajadores')->onDelete('cascade');
            $table->integer('dias_trabajados')->default(0);
            // Asignaciones devengadas
            $table->decimal('salario_devengado', 12, 2)->default(0.00);
            $table->decimal('cestaticket_devengado', 12, 2)->default(0.00);
            $table->decimal('bono_adicional', 12, 2)->default(0.00);
            $table->decimal('total_asignaciones', 12, 2)->default(0.00);
            // Deducciones de Ley (Venezuela LOTTT)
            $table->decimal('deduccion_ivss', 12, 2)->default(0.00); // 4%
            $table->decimal('deduccion_rpe', 12, 2)->default(0.00);  // 0.5% Paro Forzoso
            $table->decimal('deduccion_faov', 12, 2)->default(0.00); // 1% Hábitat y Vivienda
            $table->decimal('total_deducciones', 12, 2)->default(0.00);
            // Neto a Pagar
            $table->decimal('neto_a_pagar', 12, 2)->default(0.00);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nomina_detalles');
    }
};
