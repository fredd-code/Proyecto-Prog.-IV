<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Migración para Soporte Multi-Sede, Cupos de Trabajadores por Empresa
     * y Asignación de Horario Individual con Descanso Legal / Almuerzo (LOTTT).
     */
    public function up(): void
    {
        // 1. Tabla de Sedes / Sucursales por Empresa
        Schema::create('sedes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->cascadeOnDelete();
            $table->string('nombre', 150);
            $table->string('codigo', 30)->nullable();
            $table->string('telefono_contacto', 30)->nullable();
            $table->text('direccion')->nullable();
            // Geofencing GPS por sede
            $table->decimal('latitud', 10, 8)->default(10.24690000);
            $table->decimal('longitud', 11, 8)->default(-67.59580000);
            $table->integer('radio_geocerca_metros')->default(200);
            $table->boolean('es_principal')->default(false);
            $table->enum('estado', ['activa', 'inactiva'])->default('activa');
            $table->timestamps();
        });

        // 2. Límite de Trabajadores en Empresas (Cupo SaaS determinado por el SuperAdmin)
        Schema::table('empresas', function (Blueprint $table) {
            $table->integer('limite_trabajadores')->default(15);
        });

        // 3. Asignación de Sede y Horario Individualizado en Trabajadores
        Schema::table('trabajadores', function (Blueprint $table) {
            $table->foreignId('sede_id')->nullable()->constrained('sedes')->nullOnDelete();
            $table->time('hora_entrada')->default('08:00:00');
            $table->time('hora_salida')->default('17:00:00');
            $table->boolean('aplica_almuerzo')->default(true);
            $table->time('inicio_almuerzo')->nullable()->default('12:00:00');
            $table->time('fin_almuerzo')->nullable()->default('13:00:00');
            $table->integer('tolerancia_minutos')->default(15);
        });

        // 4. Auditoría de Asistencias por Sede y Registro de Almuerzo / Descanso
        Schema::table('asistencias', function (Blueprint $table) {
            $table->foreignId('sede_id')->nullable()->constrained('sedes')->nullOnDelete();
            $table->time('hora_salida_almuerzo')->nullable();
            $table->time('hora_entrada_almuerzo')->nullable();
        });

        // 5. Sembrar sedes principales automáticas para empresas existentes
        $empresas = DB::table('empresas')->get();
        foreach ($empresas as $emp) {
            $sedeId = DB::table('sedes')->insertGetId([
                'empresa_id'            => $emp->id,
                'nombre'                => 'Sede Principal - ' . $emp->nombre_comercial,
                'codigo'                => 'SEDE-01',
                'telefono_contacto'     => $emp->telefono_notificaciones,
                'direccion'             => $emp->direccion ?? 'Sede Central Administrativa',
                'latitud'               => $emp->latitud_sede ?? 10.38000000,
                'longitud'              => $emp->longitud_sede ?? -66.96000000,
                'radio_geocerca_metros' => $emp->radio_geocerca_metros ?? 200,
                'es_principal'          => true,
                'estado'                => 'activa',
                'created_at'            => now(),
                'updated_at'            => now(),
            ]);

            // Vincular trabajadores existentes a esta primera sede
            DB::table('trabajadores')->where('empresa_id', $emp->id)->update([
                'sede_id' => $sedeId,
                'hora_entrada' => '08:00:00',
                'hora_salida' => '17:00:00',
                'aplica_almuerzo' => true,
                'inicio_almuerzo' => '12:00:00',
                'fin_almuerzo' => '13:00:00',
                'tolerancia_minutos' => $emp->tolerancia_minutos ?? 15,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('sedes');
    }
};
