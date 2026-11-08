@extends('layouts.main_layout')

@section('titulo', 'Habit - Anúncios')

@section('conteudo')
    @include('top_bar')

    <div class="container py-5">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
            <h1 class="text-dark fw-bold m-0">Anúncios disponíveis</h1>
            @can('create', \App\Models\Anuncio::class)
                <a href="{{ route('anuncios.create') }}" class="btn btn-dark rounded-pill border border-dark px-4 fw-bold">
                    + Novo Anúncio
                </a>
            @endcan
        </div>

        <div class="d-flex gap-3 overflow-auto pb-2 mb-4">
            <a href="{{ route('home') }}" class="text-decoration-none flex-shrink-0">
                <div class="card border border-dark rounded-4 text-center p-3 {{ !$categoriaAtual ? 'bg-dark text-white' : 'bg-white text-dark' }}"
                    style="min-width: 150px;">
                    <div class="fs-2"></div>
                    <div class="fw-bold">Todos</div>
                    <div class="small">{{ $totalAnuncios }} {{ $totalAnuncios == 1 ? 'anúncio' : 'anúncios' }}</div>
                </div>
            </a>

            @foreach ($categorias as $categoria)
                <a href="{{ route('home', ['categoria' => $categoria->slug]) }}" class="text-decoration-none flex-shrink-0">
                    <div class="card border border-dark rounded-4 text-center p-3 {{ $categoriaAtual && $categoriaAtual->id == $categoria->id ? 'bg-dark text-white' : 'bg-white text-dark' }}"
                        style="min-width: 150px;">
                        <div class="fs-2">{{ $categoria->icone }}</div>
                        <div class="fw-bold">{{ $categoria->nome }}</div>
                        <div class="small">{{ $categoria->anuncios_count }} {{ $categoria->anuncios_count == 1 ? 'anúncio' : 'anúncios' }}</div>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="row row-cols-1 g-4">
            @forelse ($anuncios as $anuncio)
                <div class="col">
                    <div class="card bg-white border border-dark rounded-4 p-4 shadow-sm h-100">
                        <div class="card-body d-flex flex-column p-0">
                            @if ($anuncio->imagens->isNotEmpty())
                                <img src="{{ $anuncio->imagens->first()->url() }}" alt="{{ $anuncio->titulo }}"
                                    class="w-100 rounded-3 border border-dark mb-3"
                                    style="height: 240px; object-fit: cover;">
                            @endif
                            @if ($anuncio->categoria)
                                <div class="mb-2">
                                    <span class="badge bg-light text-dark border border-dark rounded-pill px-3 py-2">
                                        {{ $anuncio->categoria->icone }} {{ $anuncio->categoria->nome }}
                                    </span>
                                </div>
                            @endif
                            @if (in_array($anuncio->id, $anunciosComProposta))
                                <div class="mb-2">
                                    <span class="badge bg-dark text-white rounded-pill px-3 py-2">Proposta enviada</span>
                                </div>
                            @endif
                            <h4 class="card-title text-dark fw-bold mb-2">{{ $anuncio->titulo }}</h4>

                            <h5 class="text-dark fw-bolder mb-4">R$ {{ number_format($anuncio->preco, 2, ',', '.') }}</h5>

                            <div class="d-flex flex-column flex-md-row gap-md-4 mb-3">
                                <p class="card-text mb-1 text-dark"><strong>Área:</strong>
                                    {{ number_format($anuncio->area, 2, ',', '.') }} m²</p>
                                <p class="card-text mb-1 text-dark"><strong>Telefone:</strong> {{ $anuncio->telefone }}</p>
                            </div>

                            <p class="card-text text-dark mt-2 mb-4 text-truncate" style="max-height: 3rem;">
                                {{ $anuncio->descricao }}</p>

                            <div
                                class="mt-auto d-flex flex-wrap justify-content-between align-items-center gap-3 pt-3 border-top border-dark">
                                <a href="{{ route('anuncios.mostrar', ['id' => \App\Services\Operations::encryptId($anuncio->id)]) }}"
                                    class="btn btn-dark rounded-pill border border-dark fw-bold px-4">Ver Detalhes</a>
                                @can('update', $anuncio)
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
                                        </div>
                                @endcan
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert bg-white border border-dark rounded-4 text-dark text-center fw-bold p-4">
                        @if ($categoriaAtual)
                            Nenhum anúncio cadastrado em {{ $categoriaAtual->nome }} ainda.
                        @else
                            Nenhum anúncio cadastrado ainda.
                        @endif
                    </div>
                </div>
            @endforelse
        </div>
    </div>
@endsection