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
        'processed_at' => 'datetime',
    ];

    /**
     * Define o relacionamento:
     * Um Pagamento (Payment) pertence a (belongsTo) um Turno (Shift).
     */
    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class);
    }
}