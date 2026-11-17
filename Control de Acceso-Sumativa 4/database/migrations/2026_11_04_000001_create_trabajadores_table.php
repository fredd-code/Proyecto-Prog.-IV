<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecuta la migración: Tabla para registrar al personal y sus parámetros salariales.
     */
    public function up(): void
    {
        Schema::create('trabajadores', function (Blueprint $table) {
            $table->id();
            $table->string('cedula', 20)->unique();
            $table->string('nombres', 100);
            $table->string('apellidos', 100);
            $table->string('telefono', 20)->nullable();
            $table->string('correo', 100)->nullable();
            $table->string('cargo', 100);
            $table->string('departamento', 100);
            $table->date('fecha_ingreso');
            // Modalidad de cobro: quincenal o semanal
            $table->enum('frecuencia_pago', ['quincenal', 'semanal'])->default('quincenal');
            // Estructura de remuneración
            $table->decimal('salario_base', 12, 2)->default(0.00); // Salario ordinario mensual
            $table->decimal('bono_alimentacion', 12, 2)->default(0.00); // Cestaticket mensual
            $table->decimal('bono_asistencia', 12, 2)->default(0.00); // Bonificación complementaria
            $table->string('cuenta_bancaria', 30)->nullable();
            $table->enum('estado', ['activo', 'inactivo', 'vacaciones'])->default('activo');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trabajadores');
    }
};
