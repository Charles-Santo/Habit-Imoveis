<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    protected $fillable = [
        'nome',
        'slug',
        'icone',
    ];

    public function anuncios()
    {
        return $this->hasMany(Anuncio::class);
    }
}
