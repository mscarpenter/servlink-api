<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Shift;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Store a newly created resource in storage.
     * (Cria o registro de pagamento após a conclusão do Shift)
     */
    public function store(Request $request)
    {
        // Validação: Precisamos saber qual 'shift_id' está sendo pago
        $validatedData = $request->validate([
            'shift_id' => 'required|integer|exists:shifts,id',
            // (No futuro, o sistema fará isso automaticamente)
        ]);

        $shift = Shift::findOrFail($validatedData['shift_id']);
        
        // --- LÓGICA DE NEGÓCIO (Do seu roteiro estratégico) ---
        // (Aqui entraria a lógica de cálculo de comissão)
        $baseAmount = $shift->confirmed_hours * 20.00; // Exemplo: R$20/hora
        $commissionRate = 0.15; // 15% de comissão
        $commissionAmount = $baseAmount * $commissionRate;
        $totalCharge = $baseAmount + $commissionAmount;

        $payment = Payment::create([
            'shift_id' => $shift->id,
            'base_amount' => $baseAmount,
            'commission_rate' => $commissionRate,
            'commission_amount' => $commissionAmount,
            'professional_pay' => $baseAmount, // Profissional recebe o valor base total
            'total_charge_establishment' => $totalCharge, // Estabelecimento paga base + comissão
            'status' => 'processed', // Simula um pagamento processado
            'transaction_id' => 'txn_' . uniqid(), // Gera um ID de transação falso
            'processed_at' => now(),
        ]);

        return response()->json($payment, 201);
    }
    
    // (O resto das funções (index, show, etc.) ficam vazias por enquanto)
    public function index() { return Payment::all(); }
    public function show(Payment $payment) { return $payment; }
    public function update(Request $request, Payment $payment) { /* ... */ }
    public function destroy(Payment $payment) { /* ... */ }
}