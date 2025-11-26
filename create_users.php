<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\ProfilesEstablishment;
use App\Models\ProfilesProfessional;
use Illuminate\Support\Facades\Hash;

echo "Iniciando criação de usuários...\n";

// --- 1. CRIAR 6 ESTABELECIMENTOS ESPECÍFICOS ---
$estabelecimentos = [
    ['name' => 'Hotel Jurerê Exclusive', 'email' => 'est1@servlink.com'],
    ['name' => 'Restaurante Canto da Lagoa', 'email' => 'est2@servlink.com'],
    ['name' => 'Bar Top Market', 'email' => 'est3@servlink.com'],
    ['name' => 'Pousada dos Sonhos', 'email' => 'est4@servlink.com'],
    ['name' => 'Café da Praça', 'email' => 'est5@servlink.com'],
    ['name' => 'Eventos Floripa', 'email' => 'est6@servlink.com'],
];

foreach ($estabelecimentos as $est) {
    // Verificar se já existe
    if (User::where('email', $est['email'])->exists()) {
        echo "Usuário {$est['email']} já existe. Pulando.\n";
        continue;
    }

    $user = User::create([
        'name' => $est['name'],
        'email' => $est['email'],
        'password' => Hash::make('password'), // Senha padrão
        'role' => 'establishment',
    ]);

    ProfilesEstablishment::create([
        'user_id' => $user->id,
        'company_name' => $est['name'],
        'cnpj' => '00.000.000/0001-' . rand(10, 99),
        'phone' => '(48) 3000-0000',
        'address' => 'Endereço Fictício, ' . rand(1, 1000),
        'logo_url' => 'https://via.placeholder.com/150',
        'average_rating' => rand(40, 50) / 10,
    ]);
    echo "Criado estabelecimento: {$est['name']}\n";
}

// --- 2. CRIAR 15 PROFISSIONAIS ESPECÍFICOS ---
$profissionais = [
    ['name' => 'Lucas Silva', 'email' => 'prof1@servlink.com', 'job' => 'Bartender'],
    ['name' => 'Ana Cristina', 'email' => 'prof2@servlink.com', 'job' => 'Cozinheira'],
    ['name' => 'Pedro Mello', 'email' => 'prof3@servlink.com', 'job' => 'Garçom'],
    ['name' => 'Mariana Souza', 'email' => 'prof4@servlink.com', 'job' => 'Recepcionista'],
    ['name' => 'João Paulo', 'email' => 'prof5@servlink.com', 'job' => 'Auxiliar de Cozinha'],
    ['name' => 'Fernanda Lima', 'email' => 'prof6@servlink.com', 'job' => 'Bartender'],
    ['name' => 'Ricardo Oliveira', 'email' => 'prof7@servlink.com', 'job' => 'Garçom'],
    ['name' => 'Camila Santos', 'email' => 'prof8@servlink.com', 'job' => 'Cozinheira'],
    ['name' => 'Bruno Costa', 'email' => 'prof9@servlink.com', 'job' => 'Ajudante Geral'],
    ['name' => 'Patricia Rocha', 'email' => 'prof10@servlink.com', 'job' => 'Recepcionista'],
    ['name' => 'Gabriel Alves', 'email' => 'prof11@servlink.com', 'job' => 'Bartender'],
    ['name' => 'Juliana Martins', 'email' => 'prof12@servlink.com', 'job' => 'Garçonete'],
    ['name' => 'Rafael Dias', 'email' => 'prof13@servlink.com', 'job' => 'Cozinheiro'],
    ['name' => 'Larissa Pereira', 'email' => 'prof14@servlink.com', 'job' => 'Auxiliar'],
    ['name' => 'Thiago Gomes', 'email' => 'prof15@servlink.com', 'job' => 'Segurança'],
];

foreach ($profissionais as $prof) {
    if (User::where('email', $prof['email'])->exists()) {
        echo "Usuário {$prof['email']} já existe. Pulando.\n";
        continue;
    }

    $user = User::create([
        'name' => $prof['name'],
        'email' => $prof['email'],
        'password' => Hash::make('password'),
        'role' => 'professional',
    ]);

    ProfilesProfessional::create([
        'user_id' => $user->id,
        'full_name' => $prof['name'],
        'cpf' => '000.000.000-' . rand(10, 99),
        'phone' => '(48) 99999-0000',
        'bio' => 'Profissional experiente em ' . $prof['job'],
        'photo_url' => 'https://via.placeholder.com/150',
        'overall_rating' => rand(40, 50) / 10,
    ]);
    echo "Criado profissional: {$prof['name']}\n";
}

echo "Concluído!\n";
