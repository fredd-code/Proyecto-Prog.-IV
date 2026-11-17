<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Bitácora de Alertas y Notificaciones al Teléfono de la Empresa (Retardos, Inasistencias, Retiros)
     */
    public function up(): void
    {
        Schema::create('notificaciones_empresa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->cascadeOnDelete();
            $table->foreignId('trabajador_id')->nullable()->constrained('trabajadores')->nullOnDelete();
            $table->enum('tipo_evento', ['retardo', 'inasistencia', 'retiro_imprevisto', 'fuera_geocerca', 'kiosco_marcado']);
            $table->string('telefono_destino', 30);
            $table->text('mensaje');
            $table->enum('canal', ['whatsapp', 'sms'])->default('whatsapp');
            $table->enum('estado', ['enviado', 'simulado', 'fallido'])->default('enviado');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notificaciones_empresa');
    }
};
