<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    /**
     * Campos que podem ser preenchidos em massa.
     */
    protected $fillable = [
        'shift_id',
        'base_amount',
        'commission_rate',
        'commission_amount',
        'professional_pay',
        'total_charge_establishment',
        'status',
        'transaction_id',
        'processed_at',
    ];

    /**
     * Define os campos que devem ser convertidos.
     */
    protected $casts = [
        'base_amount' => 'decimal:2',
        'commission_rate' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'professional_pay' => 'decimal:2',
        'total_charge_establishment' => 'decimal:2',
        'processed_at' => 'datetime',
    ];

    /**
     * Um Pagamento pertence a um Turno.
     */
    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class);
    }

    /**
     * Calculate payment values based on shift and commission rate.
     * 
     * @param float $baseAmount Base amount to be paid to professional
     * @param float $commissionRate Commission rate (0.15 for 15%)
     * @return array Calculated payment values
     */
    public static function calculatePaymentValues(float $baseAmount, float $commissionRate = 0.15): array
    {
        $commissionAmount = round($baseAmount * $commissionRate, 2);
        $professionalPay = $baseAmount; // Professional receives full base amount
        $totalChargeEstablishment = round($baseAmount + $commissionAmount, 2);

        return [
            'base_amount' => $baseAmount,
            'commission_rate' => $commissionRate,
            'commission_amount' => $commissionAmount,
            'professional_pay' => $professionalPay,
            'total_charge_establishment' => $totalChargeEstablishment,
        ];
    }
}