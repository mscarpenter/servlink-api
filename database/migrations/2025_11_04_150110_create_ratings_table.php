<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Aplica as migrations (Cria a tabela de avaliações).
     */
    public function up(): void
    {
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();

            // Chave Estrangeira: Liga ao Turno (Shift) que está sendo avaliado.
            // A avaliação só pode ocorrer após o turno ser concluído/pago.
            $table->foreignId('shift_id')->unique()->constrained('shifts')->onDelete('cascade');
            
            // --- Quem Avaliou (Giver) e Quem Recebeu (Receiver) ---
            
            // Ambos Giver e Receiver são usuários da tabela 'users'.
            $table->foreignId('giver_user_id')->constrained('users')->comment('ID do usuário que fez a avaliação (Pode ser Profissional ou Estabelecimento).');
            $table->foreignId('receiver_user_id')->constrained('users')->comment('ID do usuário que recebeu a avaliação.');

            // Regra de Negócio: Garante que uma pessoa não pode se auto-avaliar
            // E garante que a avaliação é mútua e justa.
            $table->index(['giver_user_id', 'receiver_user_id']);
            
            // --- Detalhes da Avaliação ---
            $table->unsignedTinyInteger('score'); // Nota da avaliação (Ex: 1 a 5 estrelas)
            $table->text('comments')->nullable(); // Comentário de texto (feedback)

            $table->timestamps();
        });
    }

    /**
     * Reverte as migrations (Desfaz as alterações).
     */
    public function down(): void
    {
        Schema::dropIfExists('ratings');
    }
};