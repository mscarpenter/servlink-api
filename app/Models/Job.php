<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Job extends Model
{
    use HasFactory;

    /**
     * Campos que podem ser preenchidos em massa.
     * (Precisamos adicionar os campos da sua tabela 'jobs' aqui)
     */
    protected $fillable = [
        'establishment_id', // Precisamos confirmar se este é o nome da coluna
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
     * Define o relacionamento:
     * Uma Vaga (Job) pode ter muitas (hasMany) Candidaturas (Applications).
     */
    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    // (Adicionaremos o relacionamento com 'Establishment' depois)
}