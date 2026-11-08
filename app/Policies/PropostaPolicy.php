<?php

namespace App\Policies;

use App\Models\Proposta;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PropostaPolicy
{
    public function update(User $user, Proposta $proposta): Response
    {
        if ($proposta->user_id != $user->id) {
            return Response::deny('Você não tem permissão para alterar esta proposta.');
        }

        if ($proposta->status === Proposta::ACEITA) {
            return Response::deny('Uma proposta aceita não pode mais ser editada.');
        }

        return Response::allow();
    }

    public function delete(User $user, Proposta $proposta): Response
    {
        if ($proposta->user_id != $user->id) {
            return Response::deny('Você não tem permissão para excluir esta proposta.');
        }

        if ($proposta->status === Proposta::ACEITA) {
            return Response::deny('Uma proposta aceita não pode ser excluída.');
        }

        return Response::allow();
    }

    public function responder(User $user, Proposta $proposta): Response
    {
        if (!$proposta->anuncio || $proposta->anuncio->user_id != $user->id) {
            return Response::deny('Você não tem permissão para responder esta proposta.');
        }

        if ($proposta->status !== Proposta::PENDENTE) {
            return Response::deny('Esta proposta já foi respondida.');
        }

        return Response::allow();
    }
}
