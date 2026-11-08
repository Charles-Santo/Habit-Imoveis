<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnuncioImagem extends Model
{
    protected $table = 'anuncio_imagens';
    protected $fillable = ['anuncio_id', 'caminho'];

    public function anuncio()
    {
        return $this->belongsTo(Anuncio::class);
    }

    public function url(): string
    {
        return asset('storage/' . $this->caminho);
    }
}