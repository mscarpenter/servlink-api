<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProfilesEstablishment extends Model
{
    use HasFactory;

    protected $table = 'profiles_establishment';
    
    protected $fillable = [
        'user_id',
        'company_name',
        'cnpj',
        'address',
        'description',
        'logo_url',
        'average_rating',
    ];

    /**
     * Um Estabelecimento pertence a um Usuário.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Um Estabelecimento pode ter muitas Vagas.
     */
    public function jobs(): HasMany
    {
        return $this->hasMany(Job::class, 'establishment_id');
    }
}