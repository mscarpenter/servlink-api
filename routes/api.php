<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\RatingController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// --- ROTAS PÚBLICAS (Não precisam de login) ---
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Rota pública para listar vagas (Qualquer um pode ver as vagas)
Route::get('/jobs', [JobController::class, 'index']);


// --- ROTAS PROTEGIDAS (Exigem Autenticação via Token Sanctum) ---
// (A CORREÇÃO ESTÁ AQUI: Route::middleware)
Route::middleware('auth:sanctum')->group(function () {
    
    // Rota para buscar o usuário logado
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Rota para fazer logout (invalidar o token)
    // (Vamos adicionar a função no AuthController depois)
    // Route::post('/logout', [AuthController::class, 'logout']);

    // --- ROTAS DE APPLICATIONS (CANDIDATURAS) ---
    // (Precisa estar logado para listar ou criar)
    Route::get('/applications', [ApplicationController::class, 'index']);
    Route::post('/applications', [ApplicationController::class, 'store']);

    // --- ROTAS DE SHIFTS (TURNOS) ---
    // (Precisa estar logado para check-in/check-out)
    Route::post('/shifts', [ShiftController::class, 'store']);
    Route::put('/shifts/{shift}', [ShiftController::class, 'update']);

    // --- ROTAS DE PAGAMENTO E AVALIAÇÃO ---
    // (Precisa estar logado para pagar ou avaliar)
    Route::post('/payments', [PaymentController::class, 'store']);
    Route::post('/ratings', [RatingController::class, 'store']);
});
