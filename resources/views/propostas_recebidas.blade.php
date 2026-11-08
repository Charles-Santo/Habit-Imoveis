@extends('layouts.main_layout')

@section('titulo', 'Habit - Propostas Recebidas')

@section('conteudo')
    @include('top_bar')

    <div class="container py-5">
        <h1 class="text-dark fw-bold mb-4">Propostas recebidas</h1>

        @include('propostas_tabs', ['ativa' => 'recebidas'])

        @if (session('sucesso'))
            <div class="alert alert-success bg-white border border-dark text-dark rounded-4 fw-bold text-center">
                {{ session('sucesso') }}
            </div>
        @endif

        @if (session('erro'))
            <div class="alert alert-danger bg-white border border-dark text-dark rounded-4 fw-bold text-center">
                {{ session('erro') }}
            </div>
        @endif

        <div class="row row-cols-1 g-4">
            @forelse ($propostas as $proposta)
                @php $idCripto = \App\Services\Operations::encryptId($proposta->id); @endphp

                <div class="col">
                    <div class="card bg-white border border-dark rounded-4 p-4 shadow-sm">
                        <div class="d-flex flex-column flex-md-row justify-content-between gap-2 mb-3 pb-3 border-bottom border-dark">
                            <div>
                                <span class="text-muted small">Anúncio</span>
                                <h5 class="fw-bold text-dark m-0">
                                    <a href="{{ route('anuncios.mostrar', ['id' => \App\Services\Operations::encryptId($proposta->anuncio->id)]) }}"
                                        class="text-dark">{{ $proposta->anuncio->titulo }}</a>
                                </h5>
                                @if (!$proposta->anuncio->publico)
                                    <span class="badge bg-secondary text-white rounded-pill px-3 py-2 mt-2">Anúncio não público</span>
                                @endif
                            </div>
                            <div class="d-flex align-items-center gap-3">
                                <span class="badge {{ $proposta->statusClasse() }} rounded-pill px-3 py-2">{{ $proposta->statusLabel() }}</span>
                                <span class="text-muted small">{{ $proposta->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-3 mb-3">
                            @if ($proposta->user && $proposta->user->foto)
                                <img src="{{ $proposta->user->fotoUrl() }}" alt="Foto de {{ $proposta->user->name }}"
                                    class="rounded-circle border border-dark"
                                    style="width: 64px; height: 64px; object-fit: cover;">
                            @else
                                <div class="rounded-circle border border-dark bg-light text-dark fw-bold fs-4 d-flex justify-content-center align-items-center"
                                    style="width: 64px; height: 64px;">
                                    {{ mb_strtoupper(mb_substr($proposta->user->name ?? '?', 0, 1)) }}
                                </div>
                            @endif

                            <div>
                                <div class="fw-bold text-dark">{{ $proposta->user->name ?? 'Usuário removido' }}</div>
                                <div class="text-dark small"><strong>E-mail:</strong> {{ $proposta->user->email ?? '—' }}</div>
                                <div class="text-dark small"><strong>Telefone:</strong> {{ $proposta->telefone }}</div>
                            </div>
                        </div>

                        <p class="text-dark mb-0" style="white-space: pre-line;">{{ $proposta->mensagem }}</p>

                        @can('responder', $proposta)
                            <div class="d-flex flex-wrap gap-2 mt-4">
                                <form action="{{ route('propostas.responder') }}" method="POST"
                                    onsubmit="return confirm('Aceitar esta proposta? O anúncio deixará de aparecer para os outros clientes até você torná-lo público de novo.')">
                                    @csrf
                                    <input type="hidden" name="proposta_id" value="{{ $idCripto }}">
                                    <input type="hidden" name="acao" value="aceitar">
                                    <button type="submit" class="btn btn-dark rounded-pill border border-dark fw-bold px-4">Aceitar</button>
                                </form>

                                <form action="{{ route('propostas.responder') }}" method="POST"
                                    onsubmit="return confirm('Recusar esta proposta?')">
                                    @csrf
                                    <input type="hidden" name="proposta_id" value="{{ $idCripto }}">
                                    <input type="hidden" name="acao" value="recusar">
                                    <button type="submit"
                                        class="btn btn-outline-danger rounded-pill border border-danger fw-bold px-4">Recusar</button>
                                </form>
                            </div>
                        @endcan
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert bg-white border border-dark rounded-4 text-dark text-center fw-bold p-4">
                        Você ainda não recebeu nenhuma proposta.
                    </div>
                </div>
            @endforelse
        </div>
    </div>
@endsection
