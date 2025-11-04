<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use App\Models\Application;
use Illuminate\Http\Request;

class ShiftController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Shift::all();
    }

    /**
     * Store a newly created resource in storage.
     * (USADO PARA O CHECK-IN)
     */
    public function store(Request $request)
    {
        // Validação: Precisamos saber qual 'application_id' está fazendo check-in
        $validatedData = $request->validate([
            'application_id' => 'required|integer|exists:applications,id',
        ]);

        // Busca a candidatura (application)
        $application = Application::findOrFail($validatedData['application_id']);

        // Cria o novo turno (Shift)
        $shift = Shift::create([
            'application_id' => $application->id,
            'job_id' => $application->job_id,
            'professional_id' => $application->user_id, // Assumindo que user_id na application é o profissional
            'scheduled_start_time' => $application->job->start_time, // Pega do Job
            'scheduled_end_time' => $application->job->end_time,     // Pega do Job
            'actual_check_in_time' => now(), // Define a hora do check-in como AGORA
            'status' => 'in_progress', // Muda o status para "em andamento"
        ]);

        return response()->json($shift, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Shift $shift)
    {
        return $shift;
    }

    /**
     * Update the specified resource in storage.
     * (USADO PARA O CHECK-OUT)
     */
    public function update(Request $request, Shift $shift)
    {
        // Atualiza o turno existente com a hora de check-out
        $shift->update([
            'actual_check_out_time' => now(), // Define a hora do check-out como AGORA
            'status' => 'completed', // Muda o status para "concluído"
            // (No futuro, adicionaremos a lógica de 'confirmed_hours' aqui)
        ]);

        return response()->json($shift);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Shift $shift)
    {
        //
    }
}