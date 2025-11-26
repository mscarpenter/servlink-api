<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Shift extends Model
{
    use HasFactory;

    /**
     * Campos que podem ser preenchidos em massa.
     */
    protected $fillable = [
        'application_id',
        'job_id',
        'professional_id',
        'scheduled_start_time',
        'scheduled_end_time',
        'actual_check_in_time',
        'actual_check_out_time',
        'confirmed_hours',
        'status',
        'qr_code', // QR Code único para check-in/check-out
    ];

    /**
     * Define os campos que devem ser convertidos.
     */
    protected $casts = [
        'scheduled_start_time' => 'datetime',
        'scheduled_end_time' => 'datetime',
        'actual_check_in_time' => 'datetime',
        'actual_check_out_time' => 'datetime',
        'confirmed_hours' => 'decimal:2',
    ];

    /**
     * Um Turno pertence a uma Candidatura.
     */
    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    /**
     * Um Turno pertence a uma Vaga.
     */
    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }

    /**
     * Um Turno pertence a um Profissional (User).
     */
    public function professional(): BelongsTo
    {
        return $this->belongsTo(User::class, 'professional_id');
    }

    /**
     * Um Turno pode ter um Pagamento.
     */
    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    /**
     * Calculate confirmed hours based on check-in and check-out times.
     */
    public function calculateConfirmedHours(): float
    {
        if (!$this->actual_check_in_time || !$this->actual_check_out_time) {
            return 0;
        }

        $diffInMinutes = $this->actual_check_in_time->diffInMinutes($this->actual_check_out_time);
        return round($diffInMinutes / 60, 2);
    }
}