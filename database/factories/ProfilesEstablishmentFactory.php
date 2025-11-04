<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProfilesEstablishmentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'company_name' => fake()->company(),
            'cnpj' => fake()->unique()->numerify('##.###.###/0001-##'),
            'address' => fake()->address(),
            'description' => fake()->paragraph(2),
        ];
    }
}