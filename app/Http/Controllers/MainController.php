<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Anuncio;
use App\Models\Categoria;
use App\Models\Proposta;
use App\Models\User;
use App\Services\Operations;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class MainController extends Controller
{
    private array $regras = [
        'titulo' => 'required|string|max:50',
        'categoria_id' => 'required|exists:categorias,id',
        'email' => 'nullable|email|max:50',
        'preco' => 'required|numeric|min:0|max:9999999999',
        'area' => 'required|numeric|min:0|max:999999',
        'telefone' => 'required|string|max:20',
        'descricao' => 'required|string',
    ];

    private array $mensagens = [
        'titulo.required' => 'O campo título é obrigatório.',
        'titulo.max' => 'O título não pode ter mais de 50 caracteres.',
        'categoria_id.required' => 'Selecione uma categoria para o imóvel.',
        'categoria_id.exists' => 'A categoria selecionada é inválida.',
        'email.email' => 'Insira um endereço de e-mail válido.',
        'preco.required' => 'Informe o preço do imóvel.',
        'preco.numeric' => 'O preço precisa ser um número válido.',
        'preco.min' => 'O preço não pode ser negativo.',
        'preco.max' => 'O valor do imóvel excede o limite permitido. até 8 caracretéres',
        'area.required' => 'A área do imóvel é obrigatória.',
        'area.numeric' => 'A área precisa ser um número válido.',
        'area.max' => 'A área informada é grande demais. até 6 caracteres',
        'telefone.required' => 'O telefone para contato é obrigatório.',
        'descricao.required' => 'Escreva uma breve descrição sobre o imóvel.',
    ];

    private array $regrasImagens = [
        'imagens.*' => 'image|mimes:jpg,jpeg,png,webp|max:4096',
    ];

    private array $mensagensImagens = [
        'imagens.required' => 'Envie pelo menos uma imagem do imóvel.',
        'imagens.min' => 'Envie pelo menos uma imagem do imóvel.',
        'imagens.max' => 'Você pode enviar no máximo 10 imagens por anúncio.',
        'imagens.*.image' => 'Todos os arquivos enviados precisam ser imagens.',
        'imagens.*.mimes' => 'As imagens devem ser JPG, PNG ou WEBP.',
        'imagens.*.max' => 'Cada imagem pode ter no máximo 4 MB.',
        'imagens.*.uploaded' => 'Não foi possível enviar uma das imagens. Verifique o tamanho do arquivo.',
    ];

    public function index(Request $request)
    {
        $categorias = Categoria::withCount([
            'anuncios' => function ($q) {
                $q->where('publico', true);
            }
        ])->orderBy('id')->get();

        $slug = $request->query('categoria');
        $categoriaAtual = is_string($slug) && $slug !== ''
            ? $categorias->firstWhere('slug', $slug)
            : null;

        $query = Anuncio::with(['imagens', 'categoria'])->where('publico', true)->latest();

        if ($categoriaAtual) {
            $query->where('categoria_id', $categoriaAtual->id);
        }

        $anuncios = $query->get();
        $totalAnuncios = Anuncio::where('publico', true)->count();

        $anunciosComProposta = Auth::check()
            ? Proposta::where('user_id', Auth::id())
                ->whereIn('status', Proposta::ATIVAS)
                ->pluck('anuncio_id')
                ->all()
            : [];

        return view('homepage', compact('anuncios', 'categorias', 'categoriaAtual', 'totalAnuncios', 'anunciosComProposta'));
    }

    public function meusAnuncios()
    {
        $userId = Auth::id();

        $anuncios = Anuncio::with('imagens')->where('user_id', $userId)->get();

        return view('meus_anuncios', compact('anuncios'));
    }

    public function createAnuncio()
    {
        Gate::authorize('create', Anuncio::class);

        $categorias = Categoria::orderBy('id')->get();

        return view('create_anuncio', compact('categorias'));
    }

    public function storeAnuncio(Request $request)
    {
        Gate::authorize('create', Anuncio::class);

        $dados = $request->validate(
            array_merge($this->regras, $this->regrasImagens, ['imagens' => 'required|array|min:1|max:10']),
            array_merge($this->mensagens, $this->mensagensImagens)
        );
        $imagens = $dados['imagens'];
        unset($dados['imagens']);
        $dados['user_id'] = Auth::id();

        $anuncio = Anuncio::create($dados);

        foreach ($imagens as $imagem) {
            $anuncio->imagens()->create([
                'caminho' => $imagem->store('anuncios', 'public'),
            ]);
        }

        return redirect()->route('home')->with('sucesso', 'Anúncio cadastrado com sucesso!');
    }

    public function editAnuncio($id)
    {
        $decrypted_id = Operations::decryptId($id);
        $anuncio = Anuncio::with('imagens')->find($decrypted_id);

        if (!$anuncio) {
            return redirect()->route('home');
        }

        Gate::authorize('update', $anuncio);

        $categorias = Categoria::orderBy('id')->get();

        return view('edit_anuncio', compact('anuncio', 'categorias'));
    }

    public function updateAnuncio(Request $request)
    {
        if ($request->anuncio_id === null) {
            return redirect()->route('home');
        }

        $decrypted_id = Operations::decryptId($request->anuncio_id);
        $anuncio = Anuncio::find($decrypted_id);

        if (!$anuncio) {
            return redirect()->route('home');
        }

        Gate::authorize('update', $anuncio);

        $dados = $request->validate(
            array_merge($this->regras, $this->regrasImagens, [
                'imagens' => 'nullable|array|max:10',
                'remover_imagens' => 'nullable|array',
                'remover_imagens.*' => 'integer',
            ]),
            array_merge($this->mensagens, $this->mensagensImagens)
        );
        unset($dados['imagens'], $dados['remover_imagens']);

        $remover = $anuncio->imagens()
            ->whereIn('id', $request->input('remover_imagens', []))
            ->get();

        $novas = $request->file('imagens', []);
        $total = ($anuncio->imagens()->count() - $remover->count()) + count($novas);

        if ($total < 1) {
            return redirect()->back()
                ->withErrors(['imagens' => 'O anúncio precisa ter pelo menos uma imagem.'])
                ->withInput();
        }

        if ($total > 10) {
            return redirect()->back()
                ->withErrors(['imagens' => 'Você pode ter no máximo 10 imagens por anúncio.'])
                ->withInput();
        }

        $anuncio->update($dados);

        foreach ($remover as $imagem) {
            Storage::disk('public')->delete($imagem->caminho);
            $imagem->delete();
        }

        foreach ($novas as $imagem) {
            $anuncio->imagens()->create([
                'caminho' => $imagem->store('anuncios', 'public'),
            ]);
        }

        return redirect()->route('home')->with('sucesso', 'Anúncio atualizado com sucesso!');
    }

    public function publicarAnuncio(Request $request)
    {
        $id = Operations::decryptId($request->input('anuncio_id'));

        if (!is_numeric($id)) {
            return redirect()->route('home');
        }

        $anuncio = Anuncio::find($id);

        if (!$anuncio) {
            return redirect()->route('anuncios.meus');
        }

        Gate::authorize('publicar', $anuncio);

        DB::transaction(function () use ($anuncio) {
            $anuncio->propostas()
                ->where('status', Proposta::ACEITA)
                ->update(['status' => Proposta::RECUSADA]);

            $anuncio->update(['publico' => true]);
        });

        return redirect()->route('anuncios.meus')
            ->with('sucesso', 'O anúncio voltou a ser público e a proposta aceita foi marcada como recusada.');
    }

    public function deleteAnuncio(Request $request)
    {
        if ($request->anuncio_id === null) {
            return redirect()->route('home');
        }

        $decrypted_id = Operations::decryptId($request->anuncio_id);
        $anuncio = Anuncio::find($decrypted_id);

        if ($anuncio) {
            Gate::authorize('delete', $anuncio);

            $anuncio->delete();
        }

        return redirect()->route('home')->with('sucesso', 'Anúncio removido com sucesso!');
    }

    public function mostrar($id)
    {
        $idDescriptografado = Operations::decryptId($id);

        $anuncio = Anuncio::with(['user', 'imagens', 'categoria'])->findOrFail($idDescriptografado);

        $meuId = Auth::id();
        $ehDono = $anuncio->user_id == $meuId;

        $minhasPropostas = Proposta::where('anuncio_id', $anuncio->id)
            ->where('user_id', $meuId)
            ->latest('id')
            ->get();

        if (!$anuncio->publico && !$ehDono && $minhasPropostas->isEmpty()) {
            return redirect()->route('home');
        }

        $propostaAtiva = $minhasPropostas->first(function ($p) {
            return in_array($p->status, Proposta::ATIVAS);
        });
        $propostaRecusada = $propostaAtiva ? null : $minhasPropostas->first();

        $proponente = Auth::user();

        return view('mostrar_anuncio', compact('anuncio', 'proponente', 'ehDono', 'propostaAtiva', 'propostaRecusada'));
    }
}