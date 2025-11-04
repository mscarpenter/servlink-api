<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProfilesProfessional>
 */
class ProfilesProfessionalFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // 'user_id' será definido quando chamarmos esta factory (veremos no Seeder)
            'full_name' => fake()->name(),
            'cpf' => fake()->unique()->numerify('###.###.###-##'),
            'phone' => fake()->phoneNumber(),
            'bio' => fake()->paragraph(2),
            'skills' => json_encode(['Bartender', 'Garçom']), // Exemplo de habilidades
            'overall_rating' => fake()->randomFloat(2, 3.5, 5.0), // Nota entre 3.5 e 5.0
            'is_verified' => fake()->boolean(80), // 80% de chance de ser 'true'
        ];
    }
}