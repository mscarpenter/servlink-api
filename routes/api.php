<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\NotificationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// --- ROTAS PÚBLICAS (Não precisam de login) ---
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Rotas públicas para listar e visualizar vagas
Route::get('/jobs', [JobController::class, 'index']);
Route::get('/jobs/{job}', [JobController::class, 'show']);

// Rota pública para ver avaliações de um usuário
Route::get('/users/{userId}/ratings', [RatingController::class, 'getUserRatings']);


// --- ROTAS PROTEGIDAS (Exigem Autenticação via Token Sanctum) ---
Route::middleware('auth:sanctum')->group(function () {
    
    // Rota para buscar o usuário logado
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Rota para fazer logout
    Route::post('/logout', [AuthController::class, 'logout']);

    // --- ROTAS DE PERFIL ---
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'show']);
    Route::put('/profile/professional', [\App\Http\Controllers\ProfileController::class, 'updateProfessional'])
        ->middleware('role:professional');
    Route::put('/profile/establishment', [\App\Http\Controllers\ProfileController::class, 'updateEstablishment'])
        ->middleware('role:establishment');
    Route::post('/profile/photo', [\App\Http\Controllers\ProfileController::class, 'uploadPhoto']);
    Route::post('/profile/document', [\App\Http\Controllers\ProfileController::class, 'uploadDocument'])
        ->middleware('role:professional');

    // --- ROTAS DE BUSCA DE PROFISSIONAIS ---
    Route::get('/professionals', [\App\Http\Controllers\ProfileController::class, 'indexProfessionals']);
    Route::get('/professionals/{id}', [\App\Http\Controllers\ProfileController::class, 'showProfessional']);

    // --- ROTAS DE JOBS (VAGAS) ---
    // Apenas estabelecimentos podem criar, editar e deletar vagas
    Route::post('/jobs', [JobController::class, 'store']);
    Route::put('/jobs/{job}', [JobController::class, 'update']);
    Route::delete('/jobs/{job}', [JobController::class, 'destroy']);

    // --- ROTAS DE APPLICATIONS (CANDIDATURAS) ---
    Route::get('/applications', [ApplicationController::class, 'index']);
    Route::get('/applications/{application}', [ApplicationController::class, 'show']);
    Route::post('/applications', [ApplicationController::class, 'store']);
    Route::put('/applications/{application}', [ApplicationController::class, 'update']);

    // --- ROTAS DE SHIFTS (TURNOS) ---
    Route::get('/shifts', [ShiftController::class, 'index']);
    Route::get('/shifts/{shift}', [ShiftController::class, 'show']);
    Route::post('/shifts', [ShiftController::class, 'store']); // Check-in
    Route::put('/shifts/{shift}', [ShiftController::class, 'update']); // Check-out

    // --- ROTAS DE PAGAMENTO E AVALIAÇÃO ---
    Route::get('/payments', [PaymentController::class, 'index']);
    Route::post('/payments', [PaymentController::class, 'store']);
    Route::post('/ratings', [RatingController::class, 'store']);
    Route::get('/ratings', [RatingController::class, 'index']);

    // --- ROTAS DE NOTIFICAÇÕES ---
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount']);
    Route::put('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);
    Route::put('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
});
