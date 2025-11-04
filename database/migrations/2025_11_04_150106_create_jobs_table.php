<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->id(); // JobID
            
            // Chave Estrangeira para o Estabelecimento que postou
            $table->foreignId('establishment_id')->constrained('profiles_establishment')->onDelete('cascade');

            // --- Detalhes da Vaga (Baseado no Roteiro Estratégico) ---
            $table->string('title'); // <-- A COLUNA QUE FALTAVA!
            $table->text('description')->nullable();
            $table->string('role');
            $table->decimal('rate', 8, 2);
            $table->enum('rate_type', ['Hourly', 'Fixed'])->default('Hourly');
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->enum('status', ['Open', 'Filled', 'Completed', 'Cancelled'])->default('Open');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jobs');
    }
};