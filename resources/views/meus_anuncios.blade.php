@extends('layouts.main_layout')

@section('titulo', 'Habit - Meus Anúncios')

@section('conteudo')
    @include('top_bar')

    <div class="container py-5">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
            <h1 class="text-dark fw-bold m-0">Meus Anúncios</h1>
            <a href="{{ route('anuncios.create') }}" class="btn btn-dark rounded-pill border border-dark px-4 fw-bold">
                + Novo Anúncio
            </a>
        </div>

        @if (session('sucesso'))
            <div class="alert alert-success bg-white border border-dark text-dark rounded-4 fw-bold text-center">
                {{ session('sucesso') }}
            </div>
        @endif

        <div class="row row-cols-1 g-4">
            @forelse ($anuncios as $anuncio)
                <div class="col">
                    <div class="card bg-white border border-dark rounded-4 p-4 shadow-sm">
                        <div class="card-body d-flex flex-column p-0">
                            @if ($anuncio->imagens->isNotEmpty())
                                <img src="{{ $anuncio->imagens->first()->url() }}" alt="{{ $anuncio->titulo }}"
                                    class="w-100 rounded-3 border border-dark mb-3"
                                    style="height: 240px; object-fit: cover;">
                            @endif
                            @if (!$anuncio->publico)
                                <div class="mb-2">
                                    <span class="badge bg-secondary text-white rounded-pill px-3 py-2">Não público (proposta aceita)</span>
                                </div>
                            @endif
                            <h4 class="card-title text-dark fw-bold mb-2">{{ $anuncio->titulo }}</h4>

                            <h5 class="text-dark fw-bolder mb-4">R$ {{ number_format($anuncio->preco, 2, ',', '.') }}</h5>

                            <div class="d-flex flex-column flex-md-row gap-md-4 mb-3">
                                <p class="card-text mb-1 text-dark"><strong>Área:</strong>
                                    {{ number_format($anuncio->area, 2, ',', '.') }} m²
                                </p>
                                <p class="card-text mb-1 text-dark"><strong>Telefone:</strong> {{ $anuncio->telefone }}</p>
                                @if ($anuncio->email)
                                    <p class="card-text mb-1 text-dark"><strong>E-mail:</strong> {{ $anuncio->email }}</p>
                                @endif
                            </div>

                            <p class="card-text text-dark mt-2 mb-4">{{ $anuncio->descricao }}</p>

                            <div class="d-flex gap-2">
                                <a href="{{ route('anuncios.edit', ['id' => \App\Services\Operations::encryptId($anuncio->id)]) }}"
                                    class="btn btn-outline-dark rounded-pill border border-dark fw-bold px-4">Editar</a>

                                <form action="{{ route('anuncios.delete') }}" method="POST"
                                    onsubmit="return confirm('Excluir este anúncio?')">
                                    @csrf
                                    <input type="hidden" name="anuncio_id"
                                        value="{{ \App\Services\Operations::encryptId($anuncio->id) }}">
                                    <button type="submit"
                                        class="btn btn-outline-danger rounded-pill border border-danger fw-bold px-4">Excluir</button>
                                </form>

                                @if (!$anuncio->publico)
                                  @can('publicar', $anuncio)
                                    <form action="{{ route('anuncios.publicar') }}" method="POST"
                                        onsubmit="return confirm('Tornar este anúncio público novamente? A proposta aceita será marcada como recusada.')">
                                        @csrf
                                        <input type="hidden" name="anuncio_id"
                                            value="{{ \App\Services\Operations::encryptId($anuncio->id) }}">
                                        <button type="submit"
                                            class="btn btn-dark rounded-pill border border-dark fw-bold px-4">Tornar público</button>
                                    </form>
                                  @endcan
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert bg-white border border-dark rounded-4 text-dark text-center fw-bold p-4">
                        Você ainda não possui nenhum anúncio cadastrado.
                    </div>
                </div>
            @endforelse
        </div>
    </div>
@endsection