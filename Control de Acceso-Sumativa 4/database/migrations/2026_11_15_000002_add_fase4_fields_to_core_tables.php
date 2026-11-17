<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Expansión de tablas del núcleo para soporte Multi-Empresa, Anti-Fraude y Nómina Avanzada.
     */
    public function up(): void
    {
        // 1. users
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('empresa_id')->nullable()->constrained('empresas')->nullOnDelete();
            $table->enum('rol', ['superadmin', 'admin_empresa', 'supervisor', 'trabajador'])->default('superadmin');
        });

        // 2. trabajadores
        Schema::table('trabajadores', function (Blueprint $table) {
            $table->foreignId('empresa_id')->default(1)->constrained('empresas')->cascadeOnDelete();
            $table->string('pin_acceso', 10)->default('1234');
            $table->time('horario_entrada_custom')->nullable();
            $table->time('horario_salida_custom')->nullable();
        });

        // 3. asistencias
        Schema::table('asistencias', function (Blueprint $table) {
            $table->foreignId('empresa_id')->default(1)->constrained('empresas')->cascadeOnDelete();
            $table->enum('tipo_marcaje', ['entrada', 'salida', 'retiro_imprevisto'])->default('entrada');
            $table->float('distancia_sede_metros')->nullable();
            $table->boolean('dentro_de_geocerca')->default(true);
            $table->boolean('es_retardo')->default(false);
            $table->string('motivo_retiro', 150)->nullable();
            $table->enum('metodo_marcaje', ['movil_gps', 'kiosco_sede', 'manual_supervisor'])->default('movil_gps');
            $table->string('dispositivo_info', 255)->nullable();
            $table->string('ip_origen', 45)->nullable();
        });

        // 4. nominas
        Schema::table('nominas', function (Blueprint $table) {
            $table->foreignId('empresa_id')->default(1)->constrained('empresas')->cascadeOnDelete();
            $table->decimal('total_prestamos_deducciones', 12, 2)->default(0);
            $table->decimal('total_abonos_asignaciones', 12, 2)->default(0);
        });

        // 5. nomina_detalles
        Schema::table('nomina_detalles', function (Blueprint $table) {
            $table->decimal('prestamos_deduccion', 10, 2)->default(0);
            $table->decimal('abonos_extras', 10, 2)->default(0);
            $table->text('ajustes_detalle_json')->nullable();
        });
    }

    public function down(): void
    {
        // En SQLite migraciones inversas de columnas son ignoradas o limitadas
    }
};
