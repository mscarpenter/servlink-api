<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Job;
use App\Models\ProfilesEstablishment;
use App\Models\ProfilesProfessional;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // --- CRIAÇÃO DOS ESTABELECIMENTOS (Mariana) ---
        // Cria 10 Usuários com o "estado" de 'establishment'
        User::factory(10)->establishment()->create()->each(function ($user) {
            // Para CADA usuário establishment criado, crie um perfil para ele
            ProfilesEstablishment::factory()->create([
                'user_id' => $user->id,
            ]);
        });

        // --- CRIAÇÃO DOS PROFISSIONAIS (Lucas) ---
        // Cria 50 Usuários com o "estado" padrão ('professional')
        User::factory(50)->create()->each(function ($user) {
            // Para CADA usuário professional criado, crie um perfil para ele
            ProfilesProfessional::factory()->create([
                'user_id' => $user->id,
                'full_name' => $user->name, // Usa o mesmo nome do usuário
            ]);
        });

        // --- CRIAÇÃO DAS VAGAS (Jobs) ---
        // Pega todos os IDs dos estabelecimentos que acabamos de criar
        $establishmentIds = ProfilesEstablishment::pluck('id');

        // Cria 100 Vagas
        Job::factory(100)->create([
            // Sorteia aleatoriamente um ID de estabelecimento para ser o "dono" da vaga
            'establishment_id' => $establishmentIds->random(),
        ]);
    }
}