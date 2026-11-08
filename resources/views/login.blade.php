@extends('layouts.main_layout')

@section('titulo', 'Habit - Login')

@section('conteudo')
    <div class="container py-5 d-flex justify-content-center align-items-center" style="min-height: 80vh;">
        <div class="card bg-white border border-dark rounded-4 p-4 p-md-5 shadow-sm" style="width: 100%; max-width: 450px;">
            <a href="{{ route('home') }}" class="text-decoration-none">
                <div class="d-flex justify-content-center align-items-center mb-3 gap-2">
                    <img src="{{ asset('assets/img/icon.png') }}" alt="Ícone Habit"
                        style="height: 50px; width: 50px; object-fit: contain;">
                    <h1 class="text-dark fw-bolder mb-0 m-0">Habit</h1>
                </div>
            </a>
            <div class="text-center mb-4">
                <h5 class="text-dark fw-bold mb-1">Bem-vindo de volta!</h5>
                <p class="text-muted small mb-0">Faça login para ver anúncios e acessar sua conta.</p>
            </div>

            <form action="{{ route('login.submit') }}" method="POST" novalidate>
                @csrf

                <div class="mb-3">
                    <label for="text_email" class="form-label text-dark fw-bold">E-mail</label>
                    <input type="email" name="text_email" id="text_email" class="form-control border-dark rounded-4 p-2"
                        value="{{ old('text_email') }}" placeholder="seu@email.com">
                    @error('text_email')
                        <div class="text-danger small mt-1 fw-bold">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="text_password" class="form-label text-dark fw-bold">Senha</label>
                    <input type="password" name="text_password" id="text_password"
                        class="form-control border-dark rounded-4 p-2" placeholder="••••••••">
                    @error('text_password')
                        <div class="text-danger small mt-1 fw-bold">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit"
                    class="btn btn-dark w-100 rounded-pill border border-dark py-2 fw-bold mb-3">ENTRAR</button>
            </form>

            <a href="{{ url('/auth/github') }}" class="btn btn-dark">
                <i class="bi bi-github"></i>
                Entrar com GitHub
            </a>
            <a href="/auth/google" class="btn btn-danger">
                Entrar com Google
            </a>

            <div class="text-center">
                <span class="text-muted small">Não possui uma conta?</span>
                <a href="{{ route('register') }}" class="text-dark fw-bold text-decoration-none ms-1">Cadastre-se</a>
            </div>

            @if(session('login_error'))
                <div class="alert alert-danger bg-white border border-dark text-dark rounded-4 mt-4 mb-0 fw-bold text-center">
                    {{ session('login_error') }}
                </div>
            @endif
        </div>
    </div>
@endsection