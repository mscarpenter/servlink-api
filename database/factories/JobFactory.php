<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class JobFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // --- NOSSOS DADOS REALISTAS DE A&B ---
        $cargos = [
            'Garçom (Temporário)', 
            'Bartender (Evento)', 
            'Cozinheiro (Turno)', 
            'Auxiliar de Cozinha',
            'Recepcionista (Hotel)'
        ];
        $funcoes = [
            'Garçom', 
            'Bartender', 
            'Cozinheiro', 
            'Auxiliar de Cozinha',
            'Recepcionista'
        ];
        // -------------------------------------

        return [
            // Sorteia um dos nossos cargos
            'title' => $this->faker->randomElement($cargos), 
            
            // Coloca uma descrição fixa (sem "latim")
            'description' => 'Vaga temporária para cobrir alta temporada no setor de A&B.', 
            
            // Sorteia uma das nossas funções
            'role' => $this->faker->randomElement($funcoes), 
            
            // Mantém os valores aleatórios, que estão bons
            'rate' => $this->faker->randomFloat(2, 20, 100), 
            'rate_type' => 'Hourly',
            'start_time' => $this->faker->dateTimeBetween('+1 day', '+5 days'),
            'end_time' => $this->faker->dateTimeBetween('+6 days', '+10 days'),
            'status' => 'Open',
        ];
    }
}