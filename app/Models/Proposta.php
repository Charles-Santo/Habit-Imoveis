<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proposta extends Model
{
    public const PENDENTE = 'pendente';
    public const ACEITA = 'aceita';
    public const RECUSADA = 'recusada';

    public const ATIVAS = [self::PENDENTE, self::ACEITA];

    protected $fillable = [
        'anuncio_id',
        'user_id',
        'telefone',
        'mensagem',
        'status',
    ];

    public function anuncio()
    {
        return $this->belongsTo(Anuncio::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::ACEITA => 'Aceita',
            self::RECUSADA => 'Recusada',
            default => 'Pendente',
        };
    }

    public function statusClasse(): string
    {
        return match ($this->status) {
            self::ACEITA => 'bg-success text-white',
            self::RECUSADA => 'bg-danger text-white',
            default => 'bg-warning text-dark',
        };
    }
}
