<?php

namespace App\Http\Controllers;

use App\Models\Anuncio;
use App\Models\Proposta;
use App\Models\User;
use App\Services\Operations;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;

class PropostaController extends Controller
{
    private function regras(): array
    {
        return [
            'telefone' => 'required|string|max:20',
            'mensagem' => 'required|string|min:20|max:2000',
        ];
    }

    private function mensagens(): array
    {
        return [
            'telefone.required' => 'Informe um telefone para contato.',
            'telefone.max' => 'O telefone não pode ter mais de 20 caracteres.',
            'mensagem.required' => 'Escreva o texto da sua proposta.',
            'mensagem.min' => 'A proposta deve ter no mínimo 20 caracteres.',
            'mensagem.max' => 'A proposta não pode ter mais de 2000 caracteres.',
        ];
    }

    private function enviarEmail(?string $para, string $assunto, string $texto): void
    {
        if (!$para) {
            return;
        }

        try {
            Mail::raw($texto, function ($mensagem) use ($para, $assunto) {
                $mensagem->to($para)->subject($assunto);
            });
        } catch (\Throwable $e) {
            report($e);
        }
    }

    private function decifrar($valor)
    {
        try {
            return Crypt::decrypt($valor);
        } catch (DecryptException $e) {
            return null;
        }
    }

    public function store(Request $request)
    {
        $request->validate(
            array_merge(['anuncio_id' => 'required'], $this->regras()),
            $this->mensagens()
        );

        $anuncioId = $this->decifrar($request->input('anuncio_id'));
        $anuncio = $anuncioId ? Anuncio::find($anuncioId) : null;

        if (!$anuncio) {
            return redirect()->route('home');
        }

        $userId = Auth::id();
        $voltar = ['id' => Operations::encryptId($anuncio->id)];

        $permissao = Gate::inspect('propor', $anuncio);

        if ($permissao->denied()) {
            return redirect()->route('anuncios.mostrar', $voltar)
                ->with('proposta_error', $permissao->message());
        }

        Proposta::create([
            'anuncio_id' => $anuncio->id,
            'user_id' => $userId,
            'telefone' => $request->input('telefone'),
            'mensagem' => $request->input('mensagem'),
            'status' => Proposta::PENDENTE,
        ]);

        $proponente = User::find($userId);

        $this->enviarEmail(
            $anuncio->user?->email,
            'Nova proposta para o seu anúncio: ' . $anuncio->titulo,
            "Olá, " . ($anuncio->user->name ?? '') . "!\n\n"
            . "Você recebeu uma nova proposta para o anúncio \"" . $anuncio->titulo . "\".\n\n"
            . "Nome: " . ($proponente->name ?? '') . "\n"
            . "E-mail: " . ($proponente->email ?? '') . "\n"
            . "Telefone: " . $request->input('telefone') . "\n\n"
            . "Proposta:\n" . $request->input('mensagem') . "\n\n"
            . "Para aceitar ou recusar, acesse: " . route('propostas.recebidas') . "\n\n"
            . "Habit Imóveis"
        );

        return redirect()->route('anuncios.mostrar', $voltar)
            ->with('sucesso', 'Proposta enviada com sucesso!');
    }


    public function enviadas()
    {
        $propostas = Proposta::with('anuncio')
            ->where('user_id', Auth::id())
            ->whereHas('anuncio')
            ->latest()
            ->get();

        return view('propostas_enviadas', compact('propostas'));
    }

    public function atualizar(Request $request)
    {
        $request->validate(
            array_merge(['proposta_id' => 'required'], $this->regras()),
            $this->mensagens()
        );

        $proposta = $this->buscarProposta($request->input('proposta_id'));

        if (!$proposta) {
            return redirect()->route('propostas.enviadas');
        }

        $permissao = Gate::inspect('update', $proposta);

        if ($permissao->denied()) {
            return redirect()->route('propostas.enviadas')->with('erro', $permissao->message());
        }

        $proposta->update([
            'telefone' => $request->input('telefone'),
            'mensagem' => $request->input('mensagem'),
            'status' => Proposta::PENDENTE,
        ]);

        return redirect()->route('propostas.enviadas')->with('sucesso', 'Proposta atualizada com sucesso!');
    }

    public function excluir(Request $request)
    {
        $proposta = $this->buscarProposta($request->input('proposta_id'));

        if (!$proposta) {
            return redirect()->route('propostas.enviadas');
        }

        $permissao = Gate::inspect('delete', $proposta);

        if ($permissao->denied()) {
            return redirect()->route('propostas.enviadas')->with('erro', $permissao->message());
        }

        $proposta->delete();

        return redirect()->route('propostas.enviadas')->with('sucesso', 'Proposta excluída.');
    }

    private function buscarProposta($valorCriptografado): ?Proposta
    {
        $id = $this->decifrar($valorCriptografado);

        return $id ? Proposta::find($id) : null;
    }


    public function recebidas()
    {
        $userId = Auth::id();

        $propostas = Proposta::with(['anuncio', 'user'])
            ->whereHas('anuncio', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->latest()
            ->get();

        return view('propostas_recebidas', compact('propostas'));
    }

    public function responder(Request $request)
    {
        $request->validate([
            'proposta_id' => 'required',
            'acao' => 'required|in:aceitar,recusar',
        ]);

        $id = $this->decifrar($request->input('proposta_id'));
        $proposta = $id ? Proposta::with('anuncio')->find($id) : null;

        if (!$proposta) {
            return redirect()->route('propostas.recebidas');
        }

        $permissao = Gate::inspect('responder', $proposta);

        if ($permissao->denied()) {
            return redirect()->route('propostas.recebidas')->with('erro', $permissao->message());
        }

        if ($request->input('acao') === 'aceitar') {
            DB::transaction(function () use ($proposta) {
                $proposta->update(['status' => Proposta::ACEITA]);
                $proposta->anuncio->update(['publico' => false]);
            });

            $this->enviarEmail(
                $proposta->user?->email,
                'Sua proposta foi aceita: ' . $proposta->anuncio->titulo,
                "Olá, " . ($proposta->user->name ?? '') . "!\n\n"
                . "Boa notícia: sua proposta para o anúncio \"" . $proposta->anuncio->titulo . "\" foi aceita.\n\n"
                . "Contato do anunciante:\n"
                . "Telefone: " . $proposta->anuncio->telefone . "\n"
                . ($proposta->anuncio->email ? "E-mail: " . $proposta->anuncio->email . "\n" : '')
                . "\nVocê pode acompanhar em: " . route('propostas.enviadas') . "\n\n"
                . "Habit Imóveis"
            );

            return redirect()->route('propostas.recebidas')
                ->with('sucesso', 'Proposta aceita. O anúncio deixou de aparecer para os outros clientes.');
        }

        $proposta->update(['status' => Proposta::RECUSADA]);

        return redirect()->route('propostas.recebidas')->with('sucesso', 'Proposta recusada.');
    }
}
