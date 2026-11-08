<header class="bg-white border-bottom border-dark mb-4">
    <nav class="navbar py-3 overflow-auto">
        <div class="container d-flex justify-content-between align-items-center flex-nowrap" style="min-width: 800px;">
            
            <div class="w-100 d-flex justify-content-start">
                @auth
                    <div class="d-flex align-items-center gap-2">
                        @if (Auth::user()->foto)
                            <img src="{{ Auth::user()->fotoUrl() }}" alt="Foto de perfil"
                                class="rounded-circle border border-dark"
                                style="width: 40px; height: 40px; object-fit: cover;">
                        @else
                            <div class="rounded-circle border border-dark bg-light text-dark fw-bold d-flex justify-content-center align-items-center"
                                style="width: 40px; height: 40px;">
                                {{ mb_strtoupper(mb_substr(Auth::user()->name, 0, 1)) }}
                            </div>
                        @endif

                        <span class="text-dark fw-bold m-0" style="cursor: default;">
                            Olá, {{ Auth::user()->name }}
                        </span>
                    </div>
                @endauth
            </div>

            <a class="navbar-brand w-100 d-flex justify-content-center align-items-center gap-2 text-dark fw-bolder fs-4 m-0" href="{{ url('/') }}">
                <img src="{{ asset('assets/img/icon.png') }}" alt="Ícone Habit" height="35">
                Habit
            </a>

            <div class="w-100 d-flex justify-content-end align-items-center gap-3">
                @auth
                    
                    @can('anunciar')
                        <a href="{{ route('anuncios.meus') }}" class="text-dark fw-bold text-decoration-none">Meus Anúncios</a>
                        <a href="{{ route('propostas.recebidas') }}" class="text-dark fw-bold text-decoration-none text-nowrap">Propostas</a>
                        <a href="{{ route('anuncios.create') }}" class="btn btn-outline-dark rounded-pill border-dark fw-bold px-3 text-nowrap">
                            + Novo Anúncio
                        </a>
                    @endcan
                    
                    @cannot('anunciar')
                        <a href="{{ route('propostas.enviadas') }}" class="text-dark fw-bold text-decoration-none text-nowrap">Propostas</a>
                    @endcannot

                    <a href="{{ route('perfil.edit') }}" class="text-dark fw-bold text-decoration-none text-nowrap">Meu Perfil</a>

                    <a href="{{ route('logout') }}" class="btn btn-dark rounded-pill border-dark fw-bold px-4">Sair</a>
                    
                @else
                    
                    <a href="{{ route('login') }}" class="text-dark fw-bold text-decoration-none">Entrar</a>
                    <a href="{{ route('register') }}" class="btn btn-dark rounded-pill border-dark fw-bold px-4 text-nowrap">Cadastre-se</a>
                    
                @endauth
            </div>
            
        </div>
    </nav>
</header>
