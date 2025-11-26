<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProfilesEstablishmentFactory extends Factory
{
    // database/factories/ProfilesEstablishmentFactory.php (CÓDIGO CORRIGIDO PARA O CNPJ)

public function definition(): array
{
    // Nomes de estabelecimentos inspirados em Florianópolis
    $restaurantes = [
        'Restaurante Canto da Lagoa',
        'Hotel Jurerê Exclusive',
        'Pousada do Encanto',
        'Bar e Eventos Top Market',
        'Deck Gourmet Beira-Mar',
        'Cafeteria Ponto do Sol',
        'Buffet Eventos Ilha',
        'Pizzaria da Ponte',
        'Bar & Lounge Centro',
        'Restaurante Peixe Urbano',
    ];

    return [
        'company_name' => $this->faker->unique()->randomElement($restaurantes),
        // CORREÇÃO: O comentário foi removido para evitar o erro 'syntax error, unexpected identifier " "'
        'cnpj' => '00.000.000/0001-' . $this->faker->unique()->numberBetween(10, 99), 
        'address' => $this->faker->streetAddress(),
        'description' => $this->faker->paragraph(2), // Adicionei os campos que faltavam para evitar erros futuros
        'logo_url' => $this->faker->imageUrl(),
        'average_rating' => $this->faker->randomFloat(1, 4.0, 5.0),
    ];
}

}
