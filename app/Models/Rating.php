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
     * Define o relacionamento:
     * Uma Avaliação (Rating) pertence a (belongsTo) um Turno (Shift).
     */
    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class);
    }

    /**
     * Define o relacionamento:
     * Uma Avaliação (Rating) foi dada por (belongsTo) um Usuário (Giver).
     */
    public function giver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'giver_user_id');
    }

    /**
     * Define o relacionamento:
     * Uma Avaliação (Rating) foi recebida por (belongsTo) um Usuário (Receiver).
     */
    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_user_id');
    }
}