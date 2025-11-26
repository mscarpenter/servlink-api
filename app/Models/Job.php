<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Job extends Model
{
    use HasFactory;

    /**
     * Campos que podem ser preenchidos em massa.
     */
    protected $fillable = [
        'establishment_id',
        'title',
        'description',
        'role',
        'rate',
        'rate_type',
        'start_time',
        'end_time',
        'status',
    ];

    /**
     * Cast attributes to native types.
     */
    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'rate' => 'decimal:2',
    ];

    /**
     * Uma Vaga pertence a um Estabelecimento.
     */
    public function establishment(): BelongsTo
    {
        return $this->belongsTo(ProfilesEstablishment::class, 'establishment_id');
    }

    /**
     * Uma Vaga pode ter muitas Candidaturas.
     */
    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    /**
     * Uma Vaga pode ter muitos Turnos.
     */
    public function shifts(): HasMany
    {
        return $this->hasMany(Shift::class);
    }
}