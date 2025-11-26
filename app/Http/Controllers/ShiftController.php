<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use App\Models\Application;
use App\Models\ProfilesEstablishment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ShiftController extends Controller
{
    /**
     * Display a listing of shifts.
     * Returns shifts based on user role.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->role === 'professional') {
            // Professional sees their own shifts
            $shifts = Shift::with(['job.establishment.user', 'application'])
                ->where('professional_id', $user->id)
                ->orderBy('scheduled_start_time', 'desc')
                ->paginate(15);
        } elseif ($user->role === 'establishment') {
            // Establishment sees shifts for their jobs
            $establishment = ProfilesEstablishment::where('user_id', $user->id)->first();
            
            if (!$establishment) {
                return response()->json(['shifts' => []]);
            }

            $shifts = Shift::with(['job', 'professional', 'application'])
                ->whereHas('job', function ($query) use ($establishment) {
                    $query->where('establishment_id', $establishment->id);
                })
                ->orderBy('scheduled_start_time', 'desc')
                ->paginate(15);
        } else {
            return response()->json(['message' => 'Tipo de usuário inválido'], 400);
        }

        return response()->json($shifts);
    }

    /**
     * Store a newly created shift (Check-in).
     * This is called when a professional scans the QR code to check in.
     */
    public function store(Request $request)
    {
        // Validate that user is a professional
        if (Auth::user()->role !== 'professional') {
            return response()->json([
                'message' => 'Apenas profissionais podem fazer check-in.'
            ], 403);
        }

        // Validate input
        $validatedData = $request->validate([
            'qr_code' => 'required|string',
        ]);

        // Find shift by QR code
        $shift = Shift::where('qr_code', $validatedData['qr_code'])->first();

        if (!$shift) {
            return response()->json([
                'message' => 'QR Code inválido.'
            ], 404);
        }

        // Check authorization - only the assigned professional can check in
        if ($shift->professional_id !== Auth::id()) {
            return response()->json([
                'message' => 'Este turno não está atribuído a você.'
            ], 403);
        }

        // Check if shift is in scheduled status
        if ($shift->status !== 'scheduled') {
            return response()->json([
                'message' => 'Este turno já foi iniciado ou concluído.'
            ], 400);
        }

        // Validate check-in time window (allow check-in up to 30 minutes before and 15 minutes after scheduled start)
        $now = Carbon::now();
        $scheduledStart = Carbon::parse($shift->scheduled_start_time);
        $earliestCheckIn = $scheduledStart->copy()->subMinutes(30);
        $latestCheckIn = $scheduledStart->copy()->addMinutes(15);

        if ($now->lt($earliestCheckIn)) {
            return response()->json([
                'message' => 'Check-in muito cedo. Você pode fazer check-in a partir de ' . $earliestCheckIn->format('H:i') . '.'
            ], 400);
        }

        if ($now->gt($latestCheckIn)) {
            // Mark as no-show if check-in is too late
            $shift->update([
                'status' => 'no_show'
            ]);

            // TODO: Send notification to establishment

            return response()->json([
                'message' => 'Check-in muito tarde. O turno foi marcado como ausência (no-show).'
            ], 400);
        }

        // Perform check-in
        $shift->update([
            'actual_check_in_time' => $now,
            'status' => 'in_progress'
        ]);

        // TODO: Send notification to establishment

        $shift->load(['job', 'application']);

        return response()->json([
            'message' => 'Check-in realizado com sucesso!',
            'shift' => $shift
        ]);
    }

    /**
     * Display the specified shift.
     */
    public function show(Shift $shift)
    {
        $user = Auth::user();

        // Check authorization
        $isProfessional = $shift->professional_id === $user->id;
        
        $isEstablishmentOwner = false;
        if ($user->role === 'establishment') {
            $establishment = ProfilesEstablishment::where('user_id', $user->id)->first();
            if ($establishment) {
                $isEstablishmentOwner = $shift->job->establishment_id === $establishment->id;
            }
        }

        if (!$isProfessional && !$isEstablishmentOwner) {
            return response()->json([
                'message' => 'Você não tem permissão para visualizar este turno.'
            ], 403);
        }

        // Load relationships
        $shift->load([
            'job.establishment.user',
            'professional',
            'application',
            'payment'
        ]);

        return response()->json($shift);
    }

    /**
     * Update the specified shift (Check-out or confirm hours).
     */
    public function update(Request $request, Shift $shift)
    {
        $user = Auth::user();

        // Professional can check-out
        if ($user->role === 'professional') {
            return $this->checkOut($request, $shift);
        }

        // Establishment can confirm hours
        if ($user->role === 'establishment') {
            return $this->confirmHours($request, $shift);
        }

        return response()->json([
            'message' => 'Ação não permitida.'
        ], 403);
    }

    /**
     * Check-out (Professional).
     */
    private function checkOut(Request $request, Shift $shift)
    {
        // Check authorization
        if ($shift->professional_id !== Auth::id()) {
            return response()->json([
                'message' => 'Este turno não está atribuído a você.'
            ], 403);
        }

        // Check if shift is in progress
        if ($shift->status !== 'in_progress') {
            return response()->json([
                'message' => 'Este turno não está em andamento.'
            ], 400);
        }

        // Validate input (optional QR code for security)
        $request->validate([
            'qr_code' => 'sometimes|string',
        ]);

        if ($request->has('qr_code') && $request->qr_code !== $shift->qr_code) {
            return response()->json([
                'message' => 'QR Code inválido.'
            ], 400);
        }

        // Perform check-out
        $now = Carbon::now();
        $shift->update([
            'actual_check_out_time' => $now,
            'status' => 'completed'
        ]);

        // Calculate confirmed hours automatically
        $confirmedHours = $shift->calculateConfirmedHours();
        $shift->update(['confirmed_hours' => $confirmedHours]);

        // Create payment automatically
        \App\Http\Controllers\PaymentController::createAutomaticPayment($shift);

        $shift->load(['job', 'application', 'payment']);

        return response()->json([
            'message' => 'Check-out realizado com sucesso!',
            'confirmed_hours' => $confirmedHours,
            'shift' => $shift
        ]);
    }

    /**
     * Confirm hours (Establishment).
     */
    private function confirmHours(Request $request, Shift $shift)
    {
        // Check authorization
        $establishment = ProfilesEstablishment::where('user_id', Auth::id())->first();
        
        if (!$establishment || $shift->job->establishment_id !== $establishment->id) {
            return response()->json([
                'message' => 'Você não tem permissão para confirmar horas deste turno.'
            ], 403);
        }

        // Check if shift is completed
        if ($shift->status !== 'completed') {
            return response()->json([
                'message' => 'Apenas turnos concluídos podem ter horas confirmadas.'
            ], 400);
        }

        // Validate input
        $validatedData = $request->validate([
            'confirmed_hours' => 'required|numeric|min:0|max:24',
        ]);

        // Update confirmed hours
        $shift->update([
            'confirmed_hours' => $validatedData['confirmed_hours']
        ]);

        // TODO: Update payment amount based on confirmed hours

        $shift->load(['job', 'professional', 'application']);

        return response()->json([
            'message' => 'Horas confirmadas com sucesso!',
            'shift' => $shift
        ]);
    }

    /**
     * Generate a unique QR code for a shift.
     * This is called internally when a shift is created.
     */
    public static function generateQRCode(): string
    {
        do {
            $qrCode = 'SHIFT-' . strtoupper(Str::random(12));
        } while (Shift::where('qr_code', $qrCode)->exists());

        return $qrCode;
    }

    /**
     * Cancel a shift (no-show or cancellation).
     */
    public function destroy(Shift $shift)
    {
        $user = Auth::user();

        // Only establishment can cancel shifts
        if ($user->role !== 'establishment') {
            return response()->json([
                'message' => 'Apenas estabelecimentos podem cancelar turnos.'
            ], 403);
        }

        // Check authorization
        $establishment = ProfilesEstablishment::where('user_id', $user->id)->first();
        
        if (!$establishment || $shift->job->establishment_id !== $establishment->id) {
            return response()->json([
                'message' => 'Você não tem permissão para cancelar este turno.'
            ], 403);
        }

        // Can only cancel scheduled shifts
        if ($shift->status !== 'scheduled') {
            return response()->json([
                'message' => 'Apenas turnos agendados podem ser cancelados.'
            ], 400);
        }

        // Update status to cancelled
        $shift->update(['status' => 'cancelled']);

        // Reopen the job
        $shift->job->update(['status' => 'Open']);

        // Reopen the application
        $shift->application->update(['status' => 'pending']);

        // TODO: Send notification to professional

        return response()->json([
            'message' => 'Turno cancelado com sucesso!'
        ]);
    }
}