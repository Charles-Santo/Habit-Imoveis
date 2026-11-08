@extends('layouts.main_layout')

@section('titulo', 'Habit - Propostas Enviadas')

@section('conteudo')
    @include('top_bar')

    <div class="container py-5">
        <h1 class="text-dark fw-bold mb-4">Propostas enviadas</h1>

        @include('propostas_tabs', ['ativa' => 'enviadas'])

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
                @php
                    $editando = old('editando') == $proposta->id;
                    $idCripto = \App\Services\Operations::encryptId($proposta->id);
                    $podeMexer = $proposta->status !== 'aceita';
                @endphp

                <div class="col">
                    <div class="card bg-white border border-dark rounded-4 p-4 shadow-sm">
                        <div class="d-flex flex-column flex-md-row justify-content-between gap-2 mb-3 pb-3 border-bottom border-dark">
                            <div>
                                <span class="text-muted small">Anúncio</span>
                                <h5 class="fw-bold text-dark m-0">
                                    <a href="{{ route('anuncios.mostrar', ['id' => \App\Services\Operations::encryptId($proposta->anuncio->id)]) }}"
                                        class="text-dark">{{ $proposta->anuncio->titulo }}</a>
                                </h5>
                            </div>
                            <div class="d-flex align-items-center gap-3">
                                <span class="badge {{ $proposta->statusClasse() }} rounded-pill px-3 py-2">{{ $proposta->statusLabel() }}</span>
                                <span class="text-muted small">{{ $proposta->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                        </div>

                        <p class="text-dark mb-1"><strong>Telefone:</strong> {{ $proposta->telefone }}</p>
                        <p class="text-dark mb-3" style="white-space: pre-line;">{{ $proposta->mensagem }}</p>

                        @cannot('update', $proposta)
                            <p class="text-dark fw-bold mb-0">O anunciante aceitou esta proposta.</p>
                        @else
                            @if ($proposta->status === 'recusada')
                                <p class="text-danger fw-bold">O anunciante recusou esta proposta.</p>
                            @endif

                            <div class="d-flex flex-wrap gap-2">
                                <button type="button" class="btn btn-outline-dark rounded-pill border border-dark fw-bold px-4"
                                    data-bs-toggle="collapse" data-bs-target="#editar-{{ $proposta->id }}">Editar</button>

                                <form action="{{ route('propostas.excluir') }}" method="POST"
                                    onsubmit="return confirm('Excluir esta proposta?')">
                                    @csrf
                                    <input type="hidden" name="proposta_id" value="{{ $idCripto }}">
                                    <button type="submit"
                                        class="btn btn-outline-danger rounded-pill border border-danger fw-bold px-4">Excluir</button>
                                </form>

                                @if ($proposta->status === 'recusada' && $proposta->anuncio->publico)
                                    <a href="{{ route('anuncios.mostrar', ['id' => \App\Services\Operations::encryptId($proposta->anuncio->id)]) }}#proposta"
                                        class="btn btn-dark rounded-pill border border-dark fw-bold px-4">Fazer nova proposta</a>
                                @endif
                            </div>

                            <div class="collapse {{ $editando ? 'show' : '' }}" id="editar-{{ $proposta->id }}">
                                <form action="{{ route('propostas.atualizar') }}" method="POST" class="mt-4 pt-4 border-top border-dark" novalidate>
                                    @csrf
                                    <input type="hidden" name="proposta_id" value="{{ $idCripto }}">
                                    <input type="hidden" name="editando" value="{{ $proposta->id }}">

                                    @if ($proposta->status === 'recusada')
                                        <p class="text-muted small">Ao salvar, a proposta volta para análise do anunciante.</p>
                                    @endif

                                    <div class="mb-3">
                                        <label for="telefone-{{ $proposta->id }}" class="form-label text-dark fw-bold">Telefone para contato</label>
                                        <input type="tel" id="telefone-{{ $proposta->id }}" name="telefone" maxlength="20"
                                            class="form-control border-dark rounded-4 p-2 @if ($editando) @error('telefone') is-invalid @enderror @endif"
                                            value="{{ $editando ? old('telefone', $proposta->telefone) : $proposta->telefone }}">
                                        @if ($editando)
                                            @error('telefone')
                                                <div class="text-danger small mt-1 fw-bold">{{ $message }}</div>
                                            @enderror
                                        @endif
                                    </div>

                                    <div class="mb-3">
                                        <label for="mensagem-{{ $proposta->id }}" class="form-label text-dark fw-bold">Sua proposta</label>
                                        <textarea id="mensagem-{{ $proposta->id }}" name="mensagem" rows="5" maxlength="2000"
                                            class="form-control border-dark rounded-4 p-2 @if ($editando) @error('mensagem') is-invalid @enderror @endif">{{ $editando ? old('mensagem', $proposta->mensagem) : $proposta->mensagem }}</textarea>
                                        @if ($editando)
                                            @error('mensagem')
                                                <div class="text-danger small mt-1 fw-bold">{{ $message }}</div>
                                            @enderror
                                        @endif
                                    </div>

                                    <button type="submit" class="btn btn-dark rounded-pill border border-dark fw-bold px-4">Salvar alterações</button>
                                </form>
                            </div>
                        @endcannot
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert bg-white border border-dark rounded-4 text-dark text-center fw-bold p-4">
                        Você ainda não enviou nenhuma proposta.
                    </div>
                </div>
            @endforelse
        </div>
    </div>
@endsection
