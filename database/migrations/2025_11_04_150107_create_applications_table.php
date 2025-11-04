<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Aplica as migrations (Cria a tabela de candidaturas).
     */
    public function up(): void
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();

            // Chaves Estrangeiras: A ligação entre os dois lados do marketplace
            // 1. Liga à VAGA (Jobs)
            $table->foreignId('job_id')->constrained()->onDelete('cascade');
            // 2. Liga ao PROFISSIONAL (quem se candidatou)
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); 
            
            // --- Lógica do Status ---
            $table->enum('status', ['pending', 'accepted', 'rejected', 'withdrawn'])
                  ->default('pending')
                  ->comment('Status da candidatura: pendente, aceita, rejeitada ou retirada.');

            // Regra de Negócio: Um profissional só pode se candidatar uma vez por vaga.
            $table->unique(['job_id', 'user_id']); 

            $table->timestamps();
        });
    }

    /**
     * Reverte as migrations (Desfaz as alterações).
     */
    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};