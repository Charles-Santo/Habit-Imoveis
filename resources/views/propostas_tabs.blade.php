@can('anunciar')
    <div class="d-flex gap-2 mb-4">
        <a href="{{ route('propostas.enviadas') }}"
            class="btn rounded-pill border border-dark fw-bold px-4 {{ $ativa === 'enviadas' ? 'btn-dark' : 'btn-outline-dark' }}">Enviadas</a>
        <a href="{{ route('propostas.recebidas') }}"
            class="btn rounded-pill border border-dark fw-bold px-4 {{ $ativa === 'recebidas' ? 'btn-dark' : 'btn-outline-dark' }}">Recebidas</a>
    </div>
@endcan
