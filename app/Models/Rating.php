<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rating extends Model
{
    use HasFactory;

    /**
     * Campos que podem ser preenchidos em massa.
     */
    protected $fillable = [
        'shift_id',
        'giver_user_id',
        'receiver_user_id',
        'score',
        'comments',
    ];

    /**
     * Casts.
     */
    protected $casts = [
        'score' => 'integer',
    ];

    /**
     * Uma Avaliação pertence a um Turno.
     */
    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class);
    }

    /**
     * Uma Avaliação foi dada por um Usuário (Giver).
     */
    public function giver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'giver_user_id');
    }

    /**
     * Uma Avaliação foi recebida por um Usuário (Receiver).
     */
    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_user_id');
    }
}