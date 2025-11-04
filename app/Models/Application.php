<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
     * Define o relacionamento:
     * Uma Candidatura (Application) pertence a (belongsTo) um Usuário (User).
     */
    public function user(): BelongsTo
    {
        // Aponta para o Model 'User'
        return $this->belongsTo(User::class); 
    }

    /**
     * Define o relacionamento:
     * Uma Candidatura (Application) pertence a (belongsTo) uma Vaga (Job).
     */
    public function job(): BelongsTo
    {
        // Aponta para o Model 'Job' (que o Sail já criou)
        return $this->belongsTo(Job::class); 
    }
}
