<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Anuncio extends Model
{
    /** @use HasFactory<\Database\Factories\AnuncioFactory> */
    use HasFactory, SoftDeletes;

    /**
     *
     * @var list<string>
     */
    protected $fillable = [
        'titulo',
        'email',
        'preco',
        'area',
        'user_id',
        'categoria_id',
        'telefone',
        'descricao',
        'publico',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'preco' => 'decimal:2',
            'area' => 'decimal:2',
            'publico' => 'boolean',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function propostas()
    {
        return $this->hasMany(Proposta::class);
    }

    public function imagens()
    {
        return $this->hasMany(AnuncioImagem::class);
    }
}
