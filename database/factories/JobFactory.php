<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\ProfilesEstablishment; // Precisamos saber qual estabelecimento postou

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Job>
 */
class JobFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Define as funções possíveis que o ServLink oferece
        $roles = ['Garçom', 'Cozinheiro', 'Auxiliar de Cozinha', 'Bartender', 'Recepcionista'];
        
        // Define os status possíveis de uma vaga
        $statuses = ['Open', 'Filled', 'Completed', 'Cancelled']; // 

        return [
            // 'establishment_id' será definido quando chamarmos (veremos no Seeder)
            'title' => fake()->jobTitle() . ' (Temporário)', // Ex: "Gerente de Marketing (Temporário)"
            'description' => fake()->paragraph(3),
            'role' => $roles[array_rand($roles)], // Sorteia uma das funções
            'rate' => fake()->randomFloat(2, 15, 30), // Valor por hora entre R$15 e R$30
            'rate_type' => 'Hourly', // 
            'start_time' => fake()->dateTimeBetween('+1 day', '+7 days'), // Vaga para a próxima semana
            'end_time' => fake()->dateTimeBetween('+8 days', '+10 days'),
            'status' => $statuses[array_rand($statuses)], // Sorteia um status
        ];
    }
}