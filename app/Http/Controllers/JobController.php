<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\ProfilesEstablishment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class JobController extends Controller
{
    /**
     * Display a listing of jobs with filters and pagination.
     * Public endpoint - anyone can view jobs.
     */
    public function index(Request $request)
    {
        $query = Job::with('establishment.user');

        // Filter by role (e.g., Garçom, Cozinheiro)
        if ($request->has('role')) {
            $query->where('role', $request->role);
        }

        // Filter by status (Open, Filled, Completed, Cancelled)
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->has('start_date')) {
            $query->whereDate('start_time', '>=', $request->start_date);
        }
        if ($request->has('end_date')) {
            $query->whereDate('end_time', '<=', $request->end_date);
        }

        // Filter by rate range
        if ($request->has('min_rate')) {
            $query->where('rate', '>=', $request->min_rate);
        }
        if ($request->has('max_rate')) {
            $query->where('rate', '<=', $request->max_rate);
        }

        // Filter by rate type (Hourly or Fixed)
        if ($request->has('rate_type')) {
            $query->where('rate_type', $request->rate_type);
        }

        // Order by newest first
        $query->orderBy('created_at', 'desc');

        // Paginate results (15 per page)
        $jobs = $query->paginate(15);

        return response()->json($jobs);
    }

    /**
     * Store a newly created job.
     * Protected - only establishments can create jobs.
     */
    public function store(Request $request)
    {
        // Validate that user is an establishment
        if (Auth::user()->role !== 'establishment') {
            return response()->json([
                'message' => 'Apenas estabelecimentos podem criar vagas.'
            ], 403);
        }

        // Get establishment profile
        $establishment = ProfilesEstablishment::where('user_id', Auth::id())->first();
        
        if (!$establishment) {
            return response()->json([
                'message' => 'Perfil de estabelecimento não encontrado.'
            ], 404);
        }

        // Validate input
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'role' => 'required|string|max:100',
            'rate' => 'required|numeric|min:0|max:9999.99',
            'rate_type' => ['required', Rule::in(['Hourly', 'Fixed'])],
            'start_time' => 'required|date|after:now',
            'end_time' => 'required|date|after:start_time',
        ]);

        // Create the job
        $job = Job::create([
            'establishment_id' => $establishment->id,
            'title' => $validatedData['title'],
            'description' => $validatedData['description'],
            'role' => $validatedData['role'],
            'rate' => $validatedData['rate'],
            'rate_type' => $validatedData['rate_type'],
            'start_time' => $validatedData['start_time'],
            'end_time' => $validatedData['end_time'],
            'status' => 'Open',
        ]);

        // Load relationship for response
        $job->load('establishment.user');

        return response()->json([
            'message' => 'Vaga criada com sucesso!',
            'job' => $job
        ], 201);
    }

    /**
     * Display the specified job with all details.
     */
    public function show(Job $job)
    {
        // Eager load relationships
        $job->load([
            'establishment.user',
            'applications.professional.user'
        ]);

        return response()->json($job);
    }

    /**
     * Update the specified job.
     * Protected - only the establishment owner can update.
     */
    public function update(Request $request, Job $job)
    {
        // Check authorization - only the establishment that created the job can update it
        $establishment = ProfilesEstablishment::where('user_id', Auth::id())->first();
        
        if (!$establishment || $job->establishment_id !== $establishment->id) {
            return response()->json([
                'message' => 'Você não tem permissão para editar esta vaga.'
            ], 403);
        }

        // Validate input
        $validatedData = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'role' => 'sometimes|required|string|max:100',
            'rate' => 'sometimes|required|numeric|min:0|max:9999.99',
            'rate_type' => ['sometimes', 'required', Rule::in(['Hourly', 'Fixed'])],
            'start_time' => 'sometimes|required|date',
            'end_time' => 'sometimes|required|date|after:start_time',
            'status' => ['sometimes', 'required', Rule::in(['Open', 'Filled', 'Completed', 'Cancelled'])],
        ]);

        // Update the job
        $job->update($validatedData);

        // Reload relationships
        $job->load('establishment.user');

        return response()->json([
            'message' => 'Vaga atualizada com sucesso!',
            'job' => $job
        ]);
    }

    /**
     * Remove the specified job (soft delete by setting status to Cancelled).
     * Protected - only the establishment owner can delete.
     */
    public function destroy(Job $job)
    {
        // Check authorization
        $establishment = ProfilesEstablishment::where('user_id', Auth::id())->first();
        
        if (!$establishment || $job->establishment_id !== $establishment->id) {
            return response()->json([
                'message' => 'Você não tem permissão para cancelar esta vaga.'
            ], 403);
        }

        // Don't allow deletion if job is already filled or completed
        if (in_array($job->status, ['Filled', 'Completed'])) {
            return response()->json([
                'message' => 'Não é possível cancelar uma vaga que já foi preenchida ou concluída.'
            ], 400);
        }

        // Soft delete by changing status to Cancelled
        $job->update(['status' => 'Cancelled']);

        return response()->json([
            'message' => 'Vaga cancelada com sucesso!'
        ]);
    }
}