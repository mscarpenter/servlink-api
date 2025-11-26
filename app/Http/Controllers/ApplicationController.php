<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Job;
use App\Models\Shift;
use App\Models\ProfilesEstablishment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ApplicationController extends Controller
{
    /**
     * Display a listing of applications.
     * Returns applications based on user role.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->role === 'professional') {
            // Professional sees their own applications
            $applications = Application::with(['job.establishment.user'])
                ->where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->paginate(15);
        } elseif ($user->role === 'establishment') {
            // Establishment sees applications for their jobs
            $establishment = ProfilesEstablishment::where('user_id', $user->id)->first();
            
            if (!$establishment) {
                return response()->json(['applications' => []]);
            }

            $applications = Application::with(['job', 'user.professionalProfile'])
                ->whereHas('job', function ($query) use ($establishment) {
                    $query->where('establishment_id', $establishment->id);
                })
                ->orderBy('created_at', 'desc')
                ->paginate(15);
        } else {
            return response()->json(['message' => 'Tipo de usuário inválido'], 400);
        }

        return response()->json($applications);
    }

    /**
     * Store a newly created application.
     * Only professionals can apply to jobs.
     */
    public function store(Request $request)
    {
        // Validate that user is a professional
        if (Auth::user()->role !== 'professional') {
            return response()->json([
                'message' => 'Apenas profissionais podem se candidatar a vagas.'
            ], 403);
        }

        // Validate input
        $validatedData = $request->validate([
            'job_id' => 'required|integer|exists:jobs,id',
        ]);

        $jobId = $validatedData['job_id'];
        $userId = Auth::id();

        // Check if job is still open
        $job = Job::find($jobId);
        if ($job->status !== 'Open') {
            return response()->json([
                'message' => 'Esta vaga não está mais disponível para candidaturas.'
            ], 400);
        }

        // Check if professional already applied to this job
        $existingApplication = Application::where('job_id', $jobId)
            ->where('user_id', $userId)
            ->first();

        if ($existingApplication) {
            return response()->json([
                'message' => 'Você já se candidatou a esta vaga.',
                'application' => $existingApplication
            ], 409); // 409 = Conflict
        }

        // Create the application
        $application = Application::create([
            'job_id' => $jobId,
            'user_id' => $userId,
            'status' => 'pending',
        ]);

        // Load relationships for response
        $application->load(['job.establishment.user', 'user.professionalProfile']);

        // TODO: Send notification to establishment

        return response()->json([
            'message' => 'Candidatura enviada com sucesso!',
            'application' => $application
        ], 201);
    }

    /**
     * Display the specified application.
     */
    public function show(Application $application)
    {
        $user = Auth::user();

        // Check authorization - user must be the applicant or the establishment owner
        $isApplicant = $application->user_id === $user->id;
        
        $isEstablishmentOwner = false;
        if ($user->role === 'establishment') {
            $establishment = ProfilesEstablishment::where('user_id', $user->id)->first();
            if ($establishment) {
                $isEstablishmentOwner = $application->job->establishment_id === $establishment->id;
            }
        }

        if (!$isApplicant && !$isEstablishmentOwner) {
            return response()->json([
                'message' => 'Você não tem permissão para visualizar esta candidatura.'
            ], 403);
        }

        // Load relationships
        $application->load([
            'job.establishment.user',
            'user.professionalProfile',
            'shift'
        ]);

        return response()->json($application);
    }

    /**
     * Update the specified application (accept/reject).
     * Only establishment owners can update applications.
     */
    public function update(Request $request, Application $application)
    {
        // Validate that user is an establishment
        if (Auth::user()->role !== 'establishment') {
            return response()->json([
                'message' => 'Apenas estabelecimentos podem aceitar ou rejeitar candidaturas.'
            ], 403);
        }

        // Check authorization - only the establishment that owns the job can update
        $establishment = ProfilesEstablishment::where('user_id', Auth::id())->first();
        
        if (!$establishment || $application->job->establishment_id !== $establishment->id) {
            return response()->json([
                'message' => 'Você não tem permissão para modificar esta candidatura.'
            ], 403);
        }

        // Validate input
        $validatedData = $request->validate([
            'status' => ['required', Rule::in(['accepted', 'rejected'])],
        ]);

        $newStatus = $validatedData['status'];

        // Check if application is still pending
        if ($application->status !== 'pending') {
            return response()->json([
                'message' => 'Esta candidatura já foi processada.'
            ], 400);
        }

        // Update application status
        $application->update(['status' => $newStatus]);

        // If accepted, create a shift and update job status to Filled
        if ($newStatus === 'accepted') {
            $job = $application->job;

            // Create shift with unique QR code
            $shift = Shift::create([
                'application_id' => $application->id,
                'job_id' => $job->id,
                'professional_id' => $application->user_id,
                'scheduled_start_time' => $job->start_time,
                'scheduled_end_time' => $job->end_time,
                'qr_code' => \App\Http\Controllers\ShiftController::generateQRCode(),
                'status' => 'scheduled',
            ]);

            // Update job status to Filled
            $job->update(['status' => 'Filled']);

            // Reject all other pending applications for this job
            Application::where('job_id', $job->id)
                ->where('id', '!=', $application->id)
                ->where('status', 'pending')
                ->update(['status' => 'rejected']);

            // TODO: Send notification to professional (accepted)
            // TODO: Send notification to other applicants (rejected)

            $application->load('shift');
        }

        // Reload relationships
        $application->load(['job', 'user.professionalProfile']);

        return response()->json([
            'message' => $newStatus === 'accepted' 
                ? 'Candidatura aceita! Turno criado com sucesso.' 
                : 'Candidatura rejeitada.',
            'application' => $application
        ]);
    }

    /**
     * Remove the specified application (withdraw).
     * Only the professional who applied can withdraw.
     */
    public function destroy(Application $application)
    {
        // Check authorization - only the applicant can withdraw
        if ($application->user_id !== Auth::id()) {
            return response()->json([
                'message' => 'Você não tem permissão para retirar esta candidatura.'
            ], 403);
        }

        // Can only withdraw if status is pending
        if ($application->status !== 'pending') {
            return response()->json([
                'message' => 'Apenas candidaturas pendentes podem ser retiradas.'
            ], 400);
        }

        // Update status to withdrawn
        $application->update(['status' => 'withdrawn']);

        return response()->json([
            'message' => 'Candidatura retirada com sucesso!'
        ]);
    }
}