<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Aplica as migrations (Cria a tabela de pagamentos).
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            // Chave Estrangeira: Liga ao Turno (Shift) que está sendo pago.
            $table->foreignId('shift_id')->unique()->constrained('shifts')->onDelete('cascade');
            
            // --- Valores Financeiros (O Coração da Monetização) ---
            $table->decimal('base_amount', 8, 2); // Remuneração total DEVENDO ser paga ao Profissional
            $table->decimal('commission_rate', 4, 2); // Taxa de comissão aplicada (Ex: 0.20 para 20%) [cite: 195]
            $table->decimal('commission_amount', 8, 2); // Valor da comissão (ServLink Fee)
            
            $table->decimal('professional_pay', 8, 2); // Valor final recebido pelo Profissional (base_amount - 0)
            $table->decimal('total_charge_establishment', 8, 2); // Custo total para o Estabelecimento (base_amount + commission_amount) [cite: 197]
            
            // --- Rastreamento da Transação ---
            $table->enum('status', ['pending', 'processed', 'failed', 'refunded'])
                  ->default('pending')
                  ->comment('Status do pagamento: pendente, processado, falhou ou estornado.');
            
            $table->string('transaction_id')->nullable(); // ID da transação no gateway de pagamento
            $table->timestamp('processed_at')->nullable(); // Quando o dinheiro foi realmente transferido

            $table->timestamps();
        });
    }

    /**
     * Reverte as migrations (Desfaz as alterações).
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};