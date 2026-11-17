<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Tabla pivote para permitir que un trabajador esté asignado a una o varias sedes.
     */
    public function up(): void
    {
        Schema::create('sede_trabajador', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trabajador_id')->constrained('trabajadores')->cascadeOnDelete();
            $table->foreignId('sede_id')->constrained('sedes')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['trabajador_id', 'sede_id']);
        });

        // Migrar relaciones existentes
        $trabajadores = DB::table('trabajadores')->whereNotNull('sede_id')->get();
        foreach ($trabajadores as $t) {
            DB::table('sede_trabajador')->insertOrIgnore([
                'trabajador_id' => $t->id,
                'sede_id'       => $t->sede_id,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('sede_trabajador');
    }
};
