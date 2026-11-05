<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecuta la migración: Expediente digital de documentos por trabajador.
     */
    public function up(): void
    {
        Schema::create('expedientes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trabajador_id')->constrained('trabajadores')->onDelete('cascade');
            $table->string('titulo', 150);
            $table->enum('tipo_documento', ['cedula', 'rif', 'contrato', 'constancia_medica', 'sintesis_curricular', 'otro'])->default('otro');
            $table->string('ruta_archivo');
            $table->integer('peso_kb')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expedientes');
    }
};
