<?php

namespace App\Policies;

use App\Models\Anuncio;
use App\Models\Proposta;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Gate;

class AnuncioPolicy
{
    public function create(User $user): bool
    {
        return Gate::forUser($user)->allows('anunciar');
    }

    public function update(User $user, Anuncio $anuncio): bool
    {
        return $anuncio->user_id == $user->id;
    }

    public function delete(User $user, Anuncio $anuncio): bool
    {
        return $anuncio->user_id == $user->id;
    }

    public function publicar(User $user, Anuncio $anuncio): bool
    {
        return $anuncio->user_id == $user->id;
    }

    public function propor(User $user, Anuncio $anuncio): Response
    {
        if ($anuncio->user_id == $user->id) {
            return Response::deny('Você não pode enviar uma proposta para o seu próprio anúncio.');
        }

        if (!$anuncio->publico) {
            return Response::deny('Este anúncio não está disponível para novas propostas.');
        }

        $jaTem = Proposta::where('anuncio_id', $anuncio->id)
            ->where('user_id', $user->id)
            ->whereIn('status', Proposta::ATIVAS)
            ->exists();

        if ($jaTem) {
            return Response::deny('Você já fez uma proposta para este anúncio. Edite ou exclua a existente em "Propostas".');
        }

        return Response::allow();
    }
}
