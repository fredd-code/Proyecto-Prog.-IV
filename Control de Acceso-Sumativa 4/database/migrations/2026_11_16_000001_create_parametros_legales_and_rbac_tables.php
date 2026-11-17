<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Expansión para Panel Macro (SaaS), Parámetros Legales / Leyes Laborales
     * y Permisos Granulares por Módulos para Usuarios de Empresas (RBAC).
     */
    public function up(): void
    {
        // 1. Columnas adicionales en empresas para límites SaaS y motivos de suspensión
        Schema::table('empresas', function (Blueprint $table) {
            $table->integer('limite_sedes')->default(3);
            $table->string('motivo_suspension', 255)->nullable();
        });

        // 2. Permisos granulares y estado en users para usuarios de empresas
        Schema::table('users', function (Blueprint $table) {
            $table->string('cargo_interno', 100)->nullable();
            $table->boolean('puede_gestionar_prestamos')->default(false);
            $table->boolean('puede_aprobar_retiros')->default(false);
            $table->boolean('puede_ver_expedientes')->default(false);
            $table->boolean('puede_modificar_trabajadores')->default(false);
            $table->boolean('puede_procesar_nominas')->default(false);
            $table->boolean('puede_ver_asistencias')->default(false);
            $table->enum('estado_usuario', ['activo', 'inactivo'])->default('activo');
        });

        // Dar todos los permisos al SuperAdmin existente
        DB::table('users')->where('rol', 'superadmin')->update([
            'puede_gestionar_prestamos'   => true,
            'puede_aprobar_retiros'       => true,
            'puede_ver_expedientes'       => true,
            'puede_modificar_trabajadores'=> true,
            'puede_procesar_nominas'      => true,
            'puede_ver_asistencias'       => true,
            'cargo_interno'               => 'Operador Macro / SuperAdmin',
        ]);

        // 3. Tabla de Parámetros Legales y Normativas Laborales (LOTTT / Decretos)
        Schema::create('parametros_legales', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 50)->unique();
            $table->string('nombre', 150);
            $table->string('categoria', 50); // salarial, deducciones_ley, descansos, beneficios
            $table->string('valor', 255);
            $table->string('unidad', 30); // Bs., %, Horas, Días
            $table->string('gaceta_oficial', 120)->nullable();
            $table->text('descripcion')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        // Sembrar parámetros legales base según la normativa laboral venezolana (LOTTT)
        $parametros = [
            [
                'codigo' => 'LOTTT_CESTATICKET',
                'nombre' => 'Cestaticket Socialista de Alimentación',
                'categoria' => 'salarial',
                'valor' => '1800.00',
                'unidad' => 'Bs. / Mes',
                'gaceta_oficial' => 'G.O. N° 6.746 Extraordinario',
                'descripcion' => 'Beneficio social de alimentación mensual de carácter obligatorio y no salarial.',
            ],
            [
                'codigo' => 'LOTTT_IVSS_TRABAJADOR',
                'nombre' => 'Retención Seguro Social Obligatorio (IVSS) - Trabajador',
                'categoria' => 'deducciones_ley',
                'valor' => '4.00',
                'unidad' => '%',
                'gaceta_oficial' => 'Reglamento General LSS',
                'descripcion' => 'Aporte porcentual obligatorio del trabajador para el fondo de pensiones y salud IVSS.',
            ],
            [
                'codigo' => 'LOTTT_FAOV_TRABAJADOR',
                'nombre' => 'Fondo de Ahorro Obligatorio para la Vivienda (FAOV)',
                'categoria' => 'deducciones_ley',
                'valor' => '1.00',
                'unidad' => '%',
                'gaceta_oficial' => 'Ley del Régimen Prestacional de Vivienda',
                'descripcion' => 'Retención del 1% del salario integral mensual destinado al BANAVIH.',
            ],
            [
                'codigo' => 'LOTTT_SPF_TRABAJADOR',
                'nombre' => 'Régimen Prestacional de Empleo / Paro Forzoso',
                'categoria' => 'deducciones_ley',
                'valor' => '0.50',
                'unidad' => '%',
                'gaceta_oficial' => 'Ley de Régimen Prestacional de Empleo',
                'descripcion' => 'Deducción aplicable para la cobertura de cesantía o pérdida involuntaria del empleo.',
            ],
            [
                'codigo' => 'LOTTT_DESC_ALMUERZO',
                'nombre' => 'Descanso Legal de Alimentación y Reposo',
                'categoria' => 'descansos',
                'valor' => '1.00',
                'unidad' => 'Hora(s)',
                'gaceta_oficial' => 'LOTTT Art. 168',
                'descripcion' => 'Pausa obligatoria no imputable a la jornada efectiva de trabajo de al menos 1 hora.',
            ],
            [
                'codigo' => 'LOTTT_DIAS_VACACIONES',
                'nombre' => 'Días Mínimos de Vacaciones Anuales Remuneradas',
                'categoria' => 'beneficios',
                'valor' => '15',
                'unidad' => 'Días hábiles',
                'gaceta_oficial' => 'LOTTT Art. 190',
                'descripcion' => 'Período base al cumplir el primer año ininterrumpido de servicio laboral.',
            ],
            [
                'codigo' => 'LOTTT_UTILIDADES_MIN',
                'nombre' => 'Bonificación de Fin de Año / Utilidades Mínimas',
                'categoria' => 'beneficios',
                'valor' => '30',
                'unidad' => 'Días de salario',
                'gaceta_oficial' => 'LOTTT Art. 131',
                'descripcion' => 'Piso legal mínimo a distribuir por concepto de utilidades o bonificación patronal.',
            ],
        ];

        foreach ($parametros as $p) {
            DB::table('parametros_legales')->insert(array_merge($p, [
                'activo'     => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('parametros_legales');
    }
};
