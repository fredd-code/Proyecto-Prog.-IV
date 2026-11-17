<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Tabla de Empresas / Clientes Multi-Tenant (B2B SaaS)
     */
    public function up(): void
    {
        Schema::create('empresas', function (Blueprint $table) {
            $table->id();
            $table->string('rif', 20)->unique();
            $table->string('nombre_comercial', 150);
            $table->string('razon_social', 200);
            $table->string('telefono_notificaciones', 30);
            $table->string('email_contacto', 100)->nullable();
            $table->text('direccion')->nullable();
            // Geofencing GPS Anti-Fraude
            $table->decimal('latitud_sede', 10, 8)->default(10.24690000);
            $table->decimal('longitud_sede', 11, 8)->default(-67.59580000);
            $table->integer('radio_geocerca_metros')->default(200);
            // Configuración de Horario Laboral Base
            $table->time('horario_entrada')->default('08:00:00');
            $table->time('horario_salida')->default('17:00:00');
            $table->integer('tolerancia_minutos')->default(15);
            $table->enum('estado', ['activa', 'suspendida'])->default('activa');
            $table->timestamps();
        });

        // Insertar empresa inicial por defecto para migración segura
        DB::table('empresas')->insert([
            'id' => 1,
            'rif' => 'J-12345678-0',
            'nombre_comercial' => 'Corporación Demo UBA',
            'razon_social' => 'Corporación de Servicios y Tecnología UBA C.A.',
            'telefono_notificaciones' => '+584121234567',
            'email_contacto' => 'rrhh@empresa-demo.com',
            'direccion' => 'Av. Intercomunal, San Antonio de los Altos, Miranda',
            'latitud_sede' => 10.38000000,
            'longitud_sede' => -66.96000000,
            'radio_geocerca_metros' => 250,
            'horario_entrada' => '08:00:00',
            'horario_salida' => '17:00:00',
            'tolerancia_minutos' => 15,
            'estado' => 'activa',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

    }

    public function down(): void
    {
        Schema::dropIfExists('empresas');
    }
};
