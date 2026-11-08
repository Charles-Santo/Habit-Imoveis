@extends('layouts.main_layout')

@section('titulo', 'Habit - Editar Anúncio')

@section('conteudo')
    @include('top_bar')

    <div class="container py-5 d-flex justify-content-center">
        <div class="card bg-white border border-dark rounded-4 p-4 p-md-5 shadow-sm" style="width: 100%; max-width: 600px;">
            <h1 class="text-center text-dark fw-bold mb-4">Editar Anúncio</h1>

            <form action="{{ route('anuncios.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="anuncio_id" value="{{ \App\Services\Operations::encryptId($anuncio->id) }}">

                <div class="mb-3">
                    <label for="titulo" class="form-label text-dark fw-bold">Título</label>
                    <input type="text" id="titulo" name="titulo"
                        class="form-control border-dark rounded-4 p-2 @error('titulo') is-invalid @enderror" maxlength="50"
                        value="{{ old('titulo', $anuncio->titulo) }}">
                    @error('titulo')
                        <div class="text-danger small mt-1 fw-bold">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="categoria_id" class="form-label text-dark fw-bold">Categoria</label>
                    <select id="categoria_id" name="categoria_id"
                        class="form-select border-dark rounded-4 p-2 @error('categoria_id') is-invalid @enderror">
                        <option value="">Selecione...</option>
                        @foreach ($categorias as $categoria)
                            <option value="{{ $categoria->id }}"
                                {{ old('categoria_id', $anuncio->categoria_id) == $categoria->id ? 'selected' : '' }}>
                                {{ $categoria->icone }} {{ $categoria->nome }}
                            </option>
                        @endforeach
                    </select>
                    @error('categoria_id')
                        <div class="text-danger small mt-1 fw-bold">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label text-dark fw-bold">E-mail (opcional)</label>
                    <input type="email" id="email" name="email"
                        class="form-control border-dark rounded-4 p-2 @error('email') is-invalid @enderror" maxlength="50"
                        value="{{ old('email', $anuncio->email) }}">

                    @error('email')
                        <div class="text-danger small mt-1 fw-bold">{{ $message }}</div>
                    @enderror

                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="preco" class="form-label text-dark fw-bold">Preço (R$)</label>
                        <input type="number" step="0.01" min="0" id="preco" name="preco"
                            class="form-control border-dark rounded-4 p-2 @error('preco') is-invalid @enderror"
                            value="{{ old('preco', $anuncio->preco) }}">
                        @error('preco')
                            <div class="text-danger small mt-1 fw-bold">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="area" class="form-label text-dark fw-bold">Área (m²)</label>
                        <input type="number" step="0.01" min="0" id="area" name="area"
                            class="form-control border-dark rounded-4 p-2 @error('area') is-invalid @enderror"
                            value="{{ old('area', $anuncio->area) }}">
                        @error('area')
                            <div class="text-danger small mt-1 fw-bold">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="telefone" class="form-label text-dark fw-bold">Telefone</label>
                    <input type="tel" id="telefone" name="telefone"
                        class="form-control border-dark rounded-4 p-2 @error('telefone') is-invalid @enderror"
                        maxlength="20" value="{{ old('telefone', $anuncio->telefone) }}">
                    @error('telefone')
                        <div class="text-danger small mt-1 fw-bold">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="descricao" class="form-label text-dark fw-bold">Descrição</label>
                    <textarea id="descricao" name="descricao" rows="5"
                        class="form-control border-dark rounded-4 p-2 @error('descricao') is-invalid @enderror">{{ old('descricao', $anuncio->descricao) }}</textarea>
                    @error('descricao')
                        <div class="text-danger small mt-1 fw-bold">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="form-label text-dark fw-bold">Imagens atuais</label>
                    @if ($anuncio->imagens->isNotEmpty())
                        <div class="row g-2 mb-3">
                            @foreach ($anuncio->imagens as $imagem)
                                <div class="col-4">
                                    <img src="{{ $imagem->url() }}" alt="Imagem do imóvel"
                                        class="w-100 rounded-3 border border-dark mb-1"
                                        style="height: 100px; object-fit: cover;">
                                    <div class="form-check">
                                        <input class="form-check-input border-dark" type="checkbox"
                                            name="remover_imagens[]" id="remover_{{ $imagem->id }}"
                                            value="{{ $imagem->id }}">
                                        <label class="form-check-label small text-dark" for="remover_{{ $imagem->id }}">Remover</label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted small">Este anúncio ainda não possui imagens.</p>
                    @endif

                    <label for="imagens" class="form-label text-dark fw-bold">Adicionar novas imagens (máximo 10 no total)</label>
                    <input type="file" id="imagens" name="imagens[]" multiple
                        accept="image/jpeg,image/png,image/webp"
                        class="form-control border-dark rounded-4 p-2 @if ($errors->has('imagens') || $errors->has('imagens.*')) is-invalid @endif">
                    @error('imagens')
                        <div class="text-danger small mt-1 fw-bold">{{ $message }}</div>
                    @enderror
                    @foreach (collect($errors->get('imagens.*'))->flatten()->unique() as $msg)
                        <div class="text-danger small mt-1 fw-bold">{{ $msg }}</div>
                    @endforeach
                </div>

                <button type="submit" class="btn btn-dark w-100 rounded-pill border border-dark py-3 fw-bold fs-5">Salvar
                    alterações</button>
            </form>
        </div>
    </div>
@endsection