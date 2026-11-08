@extends('layouts.main_layout')

@section('titulo', 'Habit - Novo Anúncio')

@section('conteudo')
    @include('top_bar')

    <div class="container py-5 d-flex justify-content-center">
        <div class="card bg-white border border-dark rounded-4 p-4 p-md-5 shadow-sm" style="width: 100%; max-width: 600px;">
            <h1 class="text-center text-dark fw-bold mb-4">Novo Anúncio</h1>

            <form action="{{ route('anuncios.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label for="titulo" class="form-label text-dark fw-bold">Título</label>
                    <input type="text" id="titulo" name="titulo"
                        class="form-control border-dark rounded-4 p-2 @error('titulo') is-invalid @enderror" maxlength="50"
                        value="{{ old('titulo') }}">
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
                                {{ old('categoria_id') == $categoria->id ? 'selected' : '' }}>
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
                        value="{{ old('email') }}">
                    @error('email')
                        <div class="text-danger small mt-1 fw-bold">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="preco" class="form-label text-dark fw-bold">Preço (R$)</label>
                        <input type="number" step="0.01" min="0" id="preco" name="preco"
                            class="form-control border-dark rounded-4 p-2 @error('preco') is-invalid @enderror"
                            value="{{ old('preco') }}">
                        @error('preco')
                            <div class="text-danger small mt-1 fw-bold">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="area" class="form-label text-dark fw-bold">Área (m²)</label>
                        <input type="number" step="0.01" min="0" id="area" name="area"
                            class="form-control border-dark rounded-4 p-2 @error('area') is-invalid @enderror"
                            value="{{ old('area') }}">
                        @error('area')
                            <div class="text-danger small mt-1 fw-bold">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="telefone" class="form-label text-dark fw-bold">Telefone</label>
                    <input type="tel" id="telefone" name="telefone"
                        class="form-control border-dark rounded-4 p-2 @error('telefone') is-invalid @enderror"
                        maxlength="20" value="{{ old('telefone') }}">
                    @error('telefone')
                        <div class="text-danger small mt-1 fw-bold">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="descricao" class="form-label text-dark fw-bold">Descrição</label>
                    <textarea id="descricao" name="descricao" rows="5"
                        class="form-control border-dark rounded-4 p-2 @error('descricao') is-invalid @enderror">{{ old('descricao') }}</textarea>
                    @error('descricao')
                        <div class="text-danger small mt-1 fw-bold">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="imagens" class="form-label text-dark fw-bold">Imagens do imóvel (mínimo 1, máximo 10)</label>
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

                <button type="submit" class="btn btn-dark w-100 rounded-pill border border-dark py-3 fw-bold fs-5">Cadastrar
                    Anúncio</button>
            </form>
        </div>
    </div>
@endsection