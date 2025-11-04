<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
    ];

    /**
     * Define os campos que devem ser convertidos (ex: DATETIME).
     */
    protected $casts = [
        'scheduled_start_time' => 'datetime',
        'scheduled_end_time' => 'datetime',
        'actual_check_in_time' => 'datetime',
        'actual_check_out_time' => 'datetime',
    ];

    /**
     * Define o relacionamento:
     * Um Turno (Shift) pertence a (belongsTo) uma Candidatura (Application).
     */
    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }
}