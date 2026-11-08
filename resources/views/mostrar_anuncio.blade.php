@extends('layouts.main_layout')

@section('titulo', 'Habit - ' . $anuncio->titulo)

@section('conteudo')
    @include('top_bar')
    
    <div class="container py-5">


        <div class="card bg-white border border-dark rounded-4 p-4 p-md-5 shadow-sm">
            <div class="row g-5">
                
                <div class="col-md-7">
                    @if ($anuncio->imagens->isNotEmpty())
                        <div id="carouselImagens" class="carousel slide mb-4">
                            <div class="carousel-inner rounded-4 border border-dark">
                                @foreach ($anuncio->imagens as $imagem)
                                    <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                                        <img src="{{ $imagem->url() }}" class="d-block w-100"
                                            alt="Imagem {{ $loop->iteration }} do imóvel"
                                            style="height: 380px; object-fit: cover;">
                                    </div>
                                @endforeach
                            </div>
                            @if ($anuncio->imagens->count() > 1)
                                <button class="carousel-control-prev" type="button" data-bs-target="#carouselImagens" data-bs-slide="prev">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                    <span class="visually-hidden">Anterior</span>
                                </button>
                                <button class="carousel-control-next" type="button" data-bs-target="#carouselImagens" data-bs-slide="next">
                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                    <span class="visually-hidden">Próxima</span>
                                </button>
                            @endif
                        </div>
                    @endif

                    <div class="mb-4">
                        <span class="badge bg-dark text-white rounded-pill px-3 py-2 mb-3">Imóvel</span>
                        @if ($anuncio->categoria)
                            <span class="badge bg-light text-dark border border-dark rounded-pill px-3 py-2 mb-3 ms-1">
                                {{ $anuncio->categoria->icone }} {{ $anuncio->categoria->nome }}
                            </span>
                        @endif
                        <h2 class="text-dark fw-bold mb-2">{{ $anuncio->titulo }}</h2>
                        <h3 class="text-dark fw-bolder mb-4">R$ {{ number_format($anuncio->preco, 2, ',', '.') }}</h3>
                    </div>
                    
                    <h5 class="fw-bold border-bottom border-dark pb-2 mb-3">Descrição</h5>
                    <p class="text-dark mb-4" style="white-space: pre-line;">{{ $anuncio->descricao }}</p>

                    <h5 class="fw-bold border-bottom border-dark pb-2 mb-3">Detalhes</h5>
                    <ul class="list-unstyled text-dark mb-0">
                        <li class="mb-2 d-flex align-items-center gap-2">
                            <strong>Área Total:</strong> {{ number_format($anuncio->area, 2, ',', '.') }} m²
                        </li>
                    </ul>
                </div>

                <div class="col-md-5">
                    <div class="p-4 border border-dark rounded-4 bg-light shadow-sm h-100 d-flex flex-column">
                        
                        <h5 class="fw-bold mb-3 border-bottom border-dark pb-2">Informações do Anunciante</h5>
                        
                        <div class="mb-4">
                            @if ($anuncio->user && $anuncio->user->foto)
                                <img src="{{ $anuncio->user->fotoUrl() }}" alt="Foto do anunciante"
                                    class="rounded-circle border border-dark mb-3"
                                    style="width: 80px; height: 80px; object-fit: cover;">
                            @endif
                            <p class="mb-2"><strong>Anunciante:</strong> {{ $anuncio->user->name ?? 'Não informado' }}</p>
                            
                            @if($anuncio->user && $anuncio->user->can('anunciar'))
                                <p class="mb-2"><strong>Corretora:</strong> {{ $anuncio->user->nome_corretora ?? 'Não informado' }}</p>
                                <p class="mb-2"><strong>CNPJ:</strong> {{ $anuncio->user->cnpj_corretora ?? 'Não informado' }}</p>
                            @endif
                        </div>
                        <h5 class="fw-bold mt-auto mb-3 border-bottom border-dark pb-2">Entre em contato</h5>
                        <div class="d-flex flex-column gap-2">
                                 {{ $anuncio->telefone }}
                            
                            @if ($anuncio->email)
                                    | E-mail: {{ $anuncio->email }}
                            @endif
                        </div>

                    </div>
                </div>

            </div>
        </div>

        <div id="proposta" class="card bg-white border border-dark rounded-4 p-4 p-md-5 shadow-sm mt-4">
            <h4 class="fw-bold text-dark mb-3 border-bottom border-dark pb-2">Fazer uma proposta</h4>

            @if (session('sucesso'))
                <div class="alert alert-success bg-white border border-dark text-dark rounded-4 fw-bold text-center">
                    {{ session('sucesso') }}
                </div>
            @endif

            @if (session('proposta_error'))
                <div class="alert alert-danger bg-white border border-dark text-dark rounded-4 fw-bold text-center">
                    {{ session('proposta_error') }}
                </div>
            @endif

            @can('propor', $anuncio)
                @if ($propostaRecusada)
                    <div class="alert bg-white border border-danger rounded-4 text-dark fw-bold mb-4">
                        Sua proposta anterior foi recusada. Você pode enviar uma nova abaixo.
                    </div>
                @endif
                <div class="d-flex align-items-center gap-3 mb-4">
                    @if ($proponente && $proponente->foto)
                        <img src="{{ $proponente->fotoUrl() }}" alt="Sua foto"
                            class="rounded-circle border border-dark"
                            style="width: 64px; height: 64px; object-fit: cover;">
                    @else
                        <div class="rounded-circle border border-dark bg-light text-dark fw-bold fs-4 d-flex justify-content-center align-items-center"
                            style="width: 64px; height: 64px;">
                            {{ mb_strtoupper(mb_substr($proponente->name ?? '?', 0, 1)) }}
                        </div>
                    @endif
                    <div>
                        <div class="fw-bold text-dark">{{ $proponente->name ?? '' }}</div>
                        <div class="text-dark small">{{ $proponente->email ?? '' }}</div>
                        <div class="text-muted small">Estes dados do seu perfil serão enviados junto com a proposta.</div>
                    </div>
                </div>

                <form action="{{ route('propostas.store') }}" method="POST" novalidate>
                    @csrf
                    <input type="hidden" name="anuncio_id" value="{{ \App\Services\Operations::encryptId($anuncio->id) }}">

                    <div class="mb-3">
                        <label for="telefone_proposta" class="form-label text-dark fw-bold">Seu telefone para contato</label>
                        <input type="tel" id="telefone_proposta" name="telefone" maxlength="20"
                            class="form-control border-dark rounded-4 p-2 @error('telefone') is-invalid @enderror"
                            value="{{ old('telefone') }}">
                        @error('telefone')
                            <div class="text-danger small mt-1 fw-bold">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="mensagem" class="form-label text-dark fw-bold">Sua proposta</label>
                        <textarea id="mensagem" name="mensagem" rows="6" maxlength="2000"
                            class="form-control border-dark rounded-4 p-2 @error('mensagem') is-invalid @enderror"
                            placeholder="Escreva de forma clara o valor, as condições e o que você propõe para este imóvel.">{{ old('mensagem') }}</textarea>
                        @error('mensagem')
                            <div class="text-danger small mt-1 fw-bold">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-dark w-100 rounded-pill border border-dark py-3 fw-bold fs-5">Enviar proposta</button>
                </form>
            @else
            @if ($ehDono)
                <p class="text-dark mb-0">Este é o seu anúncio, por isso você não pode enviar uma proposta para ele.</p>
            @elseif ($propostaAtiva)
                <div class="alert bg-light border border-dark rounded-4 text-dark mb-3">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <strong>Você já fez uma proposta para este anúncio</strong>
                        <span class="badge {{ $propostaAtiva->statusClasse() }} rounded-pill px-3 py-2">{{ $propostaAtiva->statusLabel() }}</span>
                    </div>
                    <p class="mb-0" style="white-space: pre-line;">{{ $propostaAtiva->mensagem }}</p>
                </div>
                <a href="{{ route('propostas.enviadas') }}" class="btn btn-dark rounded-pill border border-dark fw-bold px-4">
                    {{ $propostaAtiva->status === 'pendente' ? 'Editar ou excluir minha proposta' : 'Ver minhas propostas' }}
                </a>
            @elseif (!$anuncio->publico)
                <p class="text-dark mb-0">Este anúncio não está disponível para novas propostas.</p>
            @endif
            @endcan
        </div>
    </div>
@endsection