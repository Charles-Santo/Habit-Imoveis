@extends('layouts.main_layout')

@section('titulo', 'Habit - Meu Perfil')

@section('conteudo')
    @include('top_bar')

    <div class="container py-5 d-flex justify-content-center">
        <div class="card bg-white border border-dark rounded-4 p-4 p-md-5 shadow-sm" style="width: 100%; max-width: 600px;">
            <h1 class="text-center text-dark fw-bold mb-4">Meu Perfil</h1>

            @if (session('sucesso'))
                <div class="alert alert-success bg-white border border-dark text-dark rounded-4 fw-bold text-center">
                    {{ session('sucesso') }}
                </div>
            @endif

            <form action="{{ route('perfil.update') }}" method="POST" enctype="multipart/form-data" novalidate>
                @csrf

                <div class="d-flex flex-column align-items-center mb-4">
                    @if ($user->foto)
                        <img src="{{ $user->fotoUrl() }}" alt="Foto de perfil"
                            class="rounded-circle border border-dark mb-3"
                            style="width: 130px; height: 130px; object-fit: cover;">
                    @else
                        <div class="rounded-circle border border-dark mb-3 d-flex justify-content-center align-items-center bg-light text-dark fw-bold fs-1"
                            style="width: 130px; height: 130px;">
                            {{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}
                        </div>
                    @endif

                    <label for="foto" class="form-label text-dark fw-bold">
                        Foto de perfil @can('anunciar') (obrigatória) @else (opcional) @endcan
                    </label>
                    <input type="file" id="foto" name="foto" accept="image/jpeg,image/png,image/webp"
                        class="form-control border-dark rounded-4 p-2">
                    @error('foto')
                        <div class="text-danger small mt-1 fw-bold">{{ $message }}</div>
                    @enderror

                    @if ($user->foto)
                        @cannot('anunciar')
                        <div class="form-check mt-2">
                            <input class="form-check-input border-dark" type="checkbox" name="remover_foto" id="remover_foto" value="1">
                            <label class="form-check-label text-dark" for="remover_foto">Remover foto atual</label>
                        </div>
                        @endcannot
                    @endif
                </div>

                <div class="mb-3">
                    <label for="name" class="form-label text-dark fw-bold">Nome Completo</label>
                    <input type="text" id="name" name="name" maxlength="255"
                        class="form-control border-dark rounded-4 p-2 @error('name') is-invalid @enderror"
                        value="{{ old('name', $user->name) }}">
                    @error('name')
                        <div class="text-danger small mt-1 fw-bold">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label text-dark fw-bold">E-mail</label>
                    <input type="email" id="email" name="email" maxlength="255"
                        class="form-control border-dark rounded-4 p-2 @error('email') is-invalid @enderror"
                        value="{{ old('email', $user->email) }}">
                    @error('email')
                        <div class="text-danger small mt-1 fw-bold">{{ $message }}</div>
                    @enderror
                </div>

                @can('anunciar')
                    <div class="p-3 mb-4 border border-dark rounded-4 bg-light">
                        <div class="mb-3">
                            <label for="nome_corretora" class="form-label text-dark fw-bold">Nome da Corretora</label>
                            <input type="text" id="nome_corretora" name="nome_corretora" maxlength="255"
                                class="form-control border-dark rounded-4 p-2 @error('nome_corretora') is-invalid @enderror"
                                value="{{ old('nome_corretora', $user->nome_corretora) }}">
                            @error('nome_corretora')
                                <div class="text-danger small mt-1 fw-bold">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-1">
                            <label for="cnpj_corretora" class="form-label text-dark fw-bold">CNPJ</label>
                            <input type="text" id="cnpj_corretora" name="cnpj_corretora" maxlength="18"
                                class="form-control border-dark rounded-4 p-2 @error('cnpj_corretora') is-invalid @enderror"
                                value="{{ old('cnpj_corretora', $user->cnpj_corretora) }}">
                            @error('cnpj_corretora')
                                <div class="text-danger small mt-1 fw-bold">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                @endcan

                <button type="submit" class="btn btn-dark w-100 rounded-pill border border-dark py-3 fw-bold fs-5">Salvar
                    alterações</button>
            </form>
        </div>
    </div>
@endsection
