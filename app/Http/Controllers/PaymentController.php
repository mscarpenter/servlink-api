<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Shift;
use App\Models\ProfilesEstablishment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    /**
     * Display a listing of payments.
     * Returns payments based on user role.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->role === 'professional') {
            // Professional sees payments for their shifts
            $payments = Payment::with(['shift.job.establishment.user'])
                ->whereHas('shift', function ($query) use ($user) {
                    $query->where('professional_id', $user->id);
                })
                ->orderBy('created_at', 'desc')
                ->paginate(15);
        } elseif ($user->role === 'establishment') {
            // Establishment sees payments for their jobs
            $establishment = ProfilesEstablishment::where('user_id', $user->id)->first();
            
            if (!$establishment) {
                return response()->json(['payments' => []]);
            }

            $payments = Payment::with(['shift.job', 'shift.professional'])
                ->whereHas('shift.job', function ($query) use ($establishment) {
                    $query->where('establishment_id', $establishment->id);
                })
                ->orderBy('created_at', 'desc')
                ->paginate(15);
        } else {
            return response()->json(['message' => 'Tipo de usuário inválido'], 400);
        }

        return response()->json($payments);
    }

    /**
     * Store a newly created payment.
     * This is called automatically after a shift is completed.
     * Can also be called manually by establishment to process payment.
     */
    public function store(Request $request)
    {
        // Validate that user is an establishment
        if (Auth::user()->role !== 'establishment') {
            return response()->json([
                'message' => 'Apenas estabelecimentos podem processar pagamentos.'
            ], 403);
        }

        // Validate input
        $validatedData = $request->validate([
            'shift_id' => 'required|integer|exists:shifts,id',
        ]);

        $shift = Shift::with(['job', 'application'])->findOrFail($validatedData['shift_id']);

        // Check authorization - only the establishment that owns the job can process payment
        $establishment = ProfilesEstablishment::where('user_id', Auth::id())->first();
        
        if (!$establishment || $shift->job->establishment_id !== $establishment->id) {
            return response()->json([
                'message' => 'Você não tem permissão para processar o pagamento deste turno.'
            ], 403);
        }

        // Check if shift is completed
        if ($shift->status !== 'completed') {
            return response()->json([
                'message' => 'Apenas turnos concluídos podem ter pagamentos processados.'
            ], 400);
        }

        // Check if payment already exists for this shift
        $existingPayment = Payment::where('shift_id', $shift->id)->first();
        if ($existingPayment) {
            return response()->json([
                'message' => 'Pagamento já foi processado para este turno.',
                'payment' => $existingPayment
            ], 409);
        }

        // Calculate base amount from shift
        $job = $shift->job;
        $confirmedHours = $shift->confirmed_hours ?? 0;
        
        // Calculate base amount based on rate type
        if ($job->rate_type === 'Hourly') {
            $baseAmount = $confirmedHours * $job->rate;
        } else { // Fixed
            $baseAmount = $job->rate;
        }

        // Calculate payment values with commission (15-20% as per strategic plan)
        $commissionRate = 0.18; // 18% commission
        $paymentValues = Payment::calculatePaymentValues($baseAmount, $commissionRate);

        // Create payment record
        $payment = Payment::create([
            'shift_id' => $shift->id,
            ...$paymentValues,
            'status' => 'pending',
            'transaction_id' => null, // Will be set when actually processed
        ]);

        // Simulate payment processing (in production, integrate with payment gateway)
        $this->processPayment($payment);

        $payment->load(['shift.job', 'shift.professional']);

        return response()->json([
            'message' => 'Pagamento criado e processado com sucesso!',
            'payment' => $payment
        ], 201);
    }

    /**
     * Display the specified payment.
     */
    public function show(Payment $payment)
    {
        $user = Auth::user();

        // Check authorization
        $isProfessional = $payment->shift->professional_id === $user->id;
        
        $isEstablishmentOwner = false;
        if ($user->role === 'establishment') {
            $establishment = ProfilesEstablishment::where('user_id', $user->id)->first();
            if ($establishment) {
                $isEstablishmentOwner = $payment->shift->job->establishment_id === $establishment->id;
            }
        }

        if (!$isProfessional && !$isEstablishmentOwner) {
            return response()->json([
                'message' => 'Você não tem permissão para visualizar este pagamento.'
            ], 403);
        }

        // Load relationships
        $payment->load([
            'shift.job.establishment.user',
            'shift.professional',
            'shift.application'
        ]);

        return response()->json($payment);
    }

    /**
     * Process payment (mock implementation).
     * In production, this would integrate with a payment gateway like Stripe or PagSeguro.
     */
    private function processPayment(Payment $payment)
    {
        // Mock payment processing
        // In production, call payment gateway API here
        
        $payment->update([
            'status' => 'processed',
            'transaction_id' => 'TXN-' . strtoupper(Str::random(16)),
            'processed_at' => now(),
        ]);

        // TODO: Send notification to professional (payment received)
        // TODO: Send notification to establishment (payment processed)
    }

    /**
     * Create payment automatically after shift completion.
     * This is called internally by ShiftController.
     */
    public static function createAutomaticPayment(Shift $shift)
    {
        // Check if payment already exists
        if (Payment::where('shift_id', $shift->id)->exists()) {
            return;
        }

        $job = $shift->job;
        $confirmedHours = $shift->confirmed_hours ?? 0;
        
        // Calculate base amount
        if ($job->rate_type === 'Hourly') {
            $baseAmount = $confirmedHours * $job->rate;
        } else {
            $baseAmount = $job->rate;
        }

        // Calculate payment values
        $commissionRate = 0.18;
        $paymentValues = Payment::calculatePaymentValues($baseAmount, $commissionRate);

        // Create payment
        $payment = Payment::create([
            'shift_id' => $shift->id,
            ...$paymentValues,
            'status' => 'pending',
        ]);

        // Auto-process payment (in production, this might be manual or triggered by webhook)
        $payment->update([
            'status' => 'processed',
            'transaction_id' => 'TXN-' . strtoupper(Str::random(16)),
            'processed_at' => now(),
        ]);

        return $payment;
    }
}