@extends('layouts.main_layout')

@section('titulo', 'Habit - Registro')

@section('conteudo')
<div class="container py-5 d-flex justify-content-center align-items-center" style="min-height: 80vh;">
    <div class="card bg-white border border-dark rounded-4 p-4 p-md-5 shadow-sm" style="width: 100%; max-width: 550px;">

        <div class="d-flex justify-content-center align-items-center mb-3 gap-2">
            <img src="{{ asset('assets/img/icon.png') }}" alt="Ícone Habit" style="height: 45px; width: 45px; object-fit: contain;">
            <h2 class="text-dark fw-bolder mb-0 m-0">Habit</h2>
        </div>

        <div class="text-center mb-4">
            <h5 class="text-dark fw-bold mb-1">Crie sua conta</h5>
            <p class="text-muted small mb-0">Junte-se ao Habit para encontrar ou anunciar os melhores imóveis da região.</p>
        </div>

        <form action="{{ route('register.submit') }}" method="POST" enctype="multipart/form-data" novalidate>
            @csrf

            <div class="mb-3">
                <label for="name" class="form-label text-dark fw-bold">Nome Completo</label>
                <input type="text" name="name" id="name" class="form-control border-dark rounded-4 p-2" value="{{ old('name') }}" placeholder="Ex: Nome Sombrenome">
                @error('name')
                    <div class="text-danger small mt-1 fw-bold">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="email" class="form-label text-dark fw-bold">E-mail</label>
                <input type="email" name="email" id="email" class="form-control border-dark rounded-4 p-2" value="{{ old('email') }}" placeholder="seu@email.com">
                @error('email')
                    <div class="text-danger small mt-1 fw-bold">{{ $message }}</div>
                @enderror
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="password" class="form-label text-dark fw-bold">Senha</label>
                    <input type="password" name="password" id="password" class="form-control border-dark rounded-4 p-2" placeholder="••••••••">
                    @error('password')
                        <div class="text-danger small mt-1 fw-bold">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="password_confirmation" class="form-label text-dark fw-bold">Confirmar Senha</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control border-dark rounded-4 p-2" placeholder="••••••••">
                </div>
            </div>

            <div class="form-check mb-3 mt-3 p-3 border border-dark rounded-4 bg-white d-flex align-items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_anunciante" id="is_anunciante" class="form-check-input border-dark m-0 fs-5" value="1" {{ old('is_anunciante') ? 'checked' : '' }} data-bs-toggle="collapse" data-bs-target="#dados_corretora" aria-expanded="{{ old('is_anunciante') ? 'true' : 'false' }}" aria-controls="dados_corretora">

                <label for="is_anunciante" class="form-check-label text-dark fw-bold" style="cursor: pointer;">
                    Desejo ser um anunciante (Corretora)
                </label>
                @error('is_anunciante')
                    <div class="text-danger small mt-1 fw-bold w-100">{{ $message }}</div>
                @enderror
            </div>

            <div id="dados_corretora" class="collapse {{ old('is_anunciante') ? 'show' : '' }} p-3 mb-4 border border-dark rounded-4 bg-light">
                <div class="mb-3">
                    <label for="nome_corretora" class="form-label text-dark fw-bold">Nome da Corretora</label>
                    <input type="text" name="nome_corretora" id="nome_corretora" class="form-control border-dark rounded-4 p-2" value="{{ old('nome_corretora') }}" placeholder="Ex: Imobiliária Habit">
                    @error('nome_corretora')
                        <div class="text-danger small mt-1 fw-bold">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="cnpj_corretora" class="form-label text-dark fw-bold">CNPJ</label>
                    <input type="text" name="cnpj_corretora" id="cnpj_corretora" class="form-control border-dark rounded-4 p-2" value="{{ old('cnpj_corretora') }}" maxlength="18" placeholder="00.000.000/0000-00">
                    @error('cnpj_corretora')
                        <div class="text-danger small mt-1 fw-bold">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-1">
                    <label for="foto" class="form-label text-dark fw-bold">Foto (obrigatória para anunciantes)</label>
                    <input type="file" name="foto" id="foto" accept="image/jpeg,image/png,image/webp" class="form-control border-dark rounded-4 p-2">
                    @error('foto')
                        <div class="text-danger small mt-1 fw-bold">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <button type="submit" class="btn btn-dark w-100 rounded-pill border border-dark py-2 fw-bold mb-3 mt-2">CRIAR CONTA</button>
        </form>

        <div class="text-center">
            <span class="text-muted small">Já possui uma conta?</span>
            <a href="{{ route('login') }}" class="text-dark fw-bold text-decoration-none ms-1">Faça login</a>
        </div>

        @if(session('register_error'))
            <div class="alert alert-danger bg-white border border-dark text-dark rounded-4 mt-4 mb-0 fw-bold text-center">
                {{ session('register_error') }}
            </div>
        @endif
    </div>
</div>
@endsection