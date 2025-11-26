<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Application extends Model
{
    use HasFactory;

    /**
     * Campos que podem ser preenchidos em massa.
     */
    protected $fillable = [
        'job_id',
        'user_id',
        'status',
    ];

    /**
     * Uma Candidatura pertence a um Usuário (Professional).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Alias para acessar o profissional via user relationship.
     */
    public function professional()
    {
        return $this->user()->with('professionalProfile');
    }

    /**
     * Uma Candidatura pertence a uma Vaga.
     */
    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }

    /**
     * Uma Candidatura pode ter um Turno (shift).
     */
    public function shift(): HasOne
    {
        return $this->hasOne(Shift::class);
    }
}
