<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use SoftDeletes;
    use HasFactory;

    public const ROLE_USER = 'user';
    public const ROLE_ANUNCIANTE = 'anunciante';
    public const ROLE_ADMIN = 'admin';

    protected $fillable = [
        'name',
        'email',
        'role',
        'corretora',
        'cnpj',
        'password',
    ];

    protected $hidden = [
        'password',
    ];

    public function anuncios()
    {
        return $this->hasMany(Anuncio::class);
    }

    public function fotoUrl(): ?string
    {
        return $this->foto ? asset('storage/' . $this->foto) : null;
    }

    public function socialAccounts(): HasMany
    {
        return $this->hasMany(SocialAccount::class);
    }
}
