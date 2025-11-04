<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfilesProfessional extends Model
{
    use HasFactory;

    /**
     * Define o nome da tabela, já que o nome do Model está no plural 
     * (Laravel procuraria por 'profiles_professionals' por padrão).
     */
    protected $table = 'profiles_professional';

    /**
     * Campos que podem ser preenchidos em massa (Mass Assignment).
     */
    protected $fillable = [
        'user_id',
        'full_name',
        'cpf',
        'phone',
        'bio',
        'photo_url',
        'skills',
        'overall_rating',
        'is_verified',
    ];

    /**
     * Define os campos que devem ser convertidos (ex: JSON para array).
     */
    protected $casts = [
        'skills' => 'array',
        'is_verified' => 'boolean',
    ];

    /**
     * Define o relacionamento inverso (1-para-1):
     * Um Perfil Profissional (ProfilesProfessional) pertence a (belongsTo) um Usuário (User).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}