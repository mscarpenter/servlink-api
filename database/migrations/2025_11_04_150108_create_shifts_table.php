<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Aplica as migrations (Cria a tabela de turnos).
     */
    public function up(): void
    {
        Schema::create('shifts', function (Blueprint $table) {
            $table->id();

            // Chaves Estrangeiras
            // 1. Liga à Candidatura ACEITA (a base do contrato)
            // Assumimos que o Shift é criado quando a Application é 'accepted'
            $table->foreignId('application_id')->unique()->constrained('applications')->onDelete('cascade');
            
            // 2. Opcional, mas útil: Liga diretamente ao Profissional e Job (para consultas mais rápidas)
            $table->foreignId('job_id')->constrained()->onDelete('cascade');
            $table->foreignId('professional_id')->constrained('profiles_professional')->onDelete('cascade');

            // --- Lógica de Tempo (O Coração da Operação) ---
            $table->dateTime('scheduled_start_time'); // Horário planejado (do Job)
            $table->dateTime('scheduled_end_time');   // Horário planejado (do Job)
            
            $table->dateTime('actual_check_in_time')->nullable(); // Onde o profissional escaneia o QR Code [cite: 145]
            $table->dateTime('actual_check_out_time')->nullable(); // Onde o profissional escaneia o QR Code 

            $table->decimal('confirmed_hours', 5, 2)->nullable(); // Horas finais confirmadas pelo Estabelecimento
            
            // QR Code único para check-in/check-out
            $table->string('qr_code')->unique()->nullable();
            
            // --- Lógica de Status (Jurídico/Financeiro) ---
            $table->enum('status', ['scheduled', 'in_progress', 'completed', 'no_show', 'cancelled'])
                  ->default('scheduled')
                  ->comment('Status do turno: agendado, em andamento, concluído, não compareceu, cancelado.');

            $table->timestamps();
        });
    }

    /**
     * Reverte as migrations (Desfaz as alterações).
     */
    public function down(): void
    {
        Schema::dropIfExists('shifts');
    }
};