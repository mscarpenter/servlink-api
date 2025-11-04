<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Aplica as migrations (Cria a tabela de perfis de profissionais).
     */
    public function up(): void
    {
        // 1. Atualizar a tabela 'users' para adicionar a coluna 'role' (se ela ainda não existir)
        // Isso garante que todo usuário seja classificado como 'professional', 'establishment' ou 'admin'.
        Schema::table('users', function (Blueprint $table) {
            // Verifica se a coluna já existe antes de tentar criar para evitar erros
            if (!Schema::hasColumn('users', 'role')) {
                $table->enum('role', ['professional', 'establishment', 'admin'])
                      ->default('professional')
                      ->after('name'); 
            }
        });

        // 2. Criar a tabela 'profiles_professional' (Detalhes de Perfis PF)
        Schema::create('profiles_professional', function (Blueprint $table) {
            $table->id(); // ID primário da tabela de perfis 
            
            // Chave Estrangeira ÚNICA: Garante que 1 usuário só pode ser 1 profissional.
            $table->foreignId('user_id')->unique()->constrained('users')->onDelete('cascade');

            // Informações Pessoais
            $table->string('full_name');
            $table->string('cpf', 14)->unique()->nullable();
            $table->string('phone', 20)->nullable();
            $table->text('bio')->nullable();
            $table->string('photo_url')->nullable();
            
            // Habilidades e Reputação
            $table->json('skills')->nullable(); // JSON para armazenar múltiplas habilidades (ex: Bartender, Cozinheiro)
            $table->decimal('overall_rating', 3, 2)->default(5.00); // Avaliação média (0 a 5)
            $table->boolean('is_verified')->default(false); // Selo de verificação do ServLink

            $table->timestamps(); // created_at e updated_at
        });
    }

    /**
     * Reverte as migrations (Desfaz as alterações).
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles_professional');
        
        // Remoção da coluna 'role' é feita na migration de 'create_profiles_establishment'
        // Mas se esta for a primeira migration, vamos deixá-la aqui como fallback
        // Para simplificar, vamos deixar a remoção da 'role' fora desta migration,
        // focando apenas na criação e remoção da tabela de perfil.
        
        // NOTA: Para um projeto profissional, a remoção do 'role' seria coordenada.
        // Aqui, focamos em não quebrar o banco de dados.
    }
};