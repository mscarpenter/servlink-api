<?php

namespace App\Http\Controllers;

use App\Models\Application; // 1. Importa o Model 'Application'
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Por enquanto, vamos retornar todas as candidaturas
        return Application::all();
    }

    /**
     * Store a newly created resource in storage.
     * Esta é a função que cria a candidatura.
     */
    public function store(Request $request)
    {
        // 2. Validação (Garantir que recebemos os dados certos)
        // Por enquanto, vamos apenas pegar os dados que o app enviaria
        $validatedData = $request->validate([
            'job_id' => 'required|integer',
            'user_id' => 'required|integer', // (No futuro, pegaremos isso do usuário logado)
        ]);

        // 3. Cria a candidatura no banco de dados
        $application = Application::create([
            'job_id' => $validatedData['job_id'],
            'user_id' => $validatedData['user_id'],
            'status' => 'pending', // Define o status padrão
        ]);

        // 4. Retorna uma resposta de sucesso (Código 201 - Created)
        return response()->json($application, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Application $application)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Application $application)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Application $application)
    {
        //
    }
}