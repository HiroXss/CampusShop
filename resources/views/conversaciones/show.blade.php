@extends('layouts.public')

@section('title', 'Conversación')

@section('content')
    @php
        $soyComprador = auth()->id() === $conversacion->comprador_id;
        $otro = $soyComprador ? $conversacion->emprendimiento : $conversacion->comprador;
        $avatar = $soyComprador ? $conversacion->emprendimiento->logo : $conversacion->comprador->avatar;
        $ultimoId = optional($mensajes->last())->id ?? 0;
    @endphp

    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <a href="{{ route('conversaciones.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i></a>
                    <img src="{{ $avatar ? Storage::url($avatar) : ($soyComprador ? asset('img/logo-placeholder.svg') : asset('img/avatar-placeholder.svg')) }}"
                         alt="" class="rounded-circle" style="width:40px;height:40px;object-fit:cover;">
                    <div>
                        <div class="fw-semibold">{{ $otro->name ?? $otro->nombre }}</div>
                        @if ($soyComprador)
                            <a href="{{ route('emprendimientos.show', $conversacion->emprendimiento) }}" class="small">Ver tienda</a>
                        @endif
                    </div>
                </div>

                @if ($conversacion->producto)
                    <div class="alert alert-light border d-flex align-items-center gap-2 small">
                        <i class="bi bi-box-seam"></i>
                        Conversación sobre: <a href="{{ route('catalogo.producto', $conversacion->producto) }}" class="fw-medium">{{ $conversacion->producto->nombre }}</a>
                    </div>
                @endif

                <div class="cs-card p-3">
                    <div id="chat-messages" class="chat-window mb-3"
                         data-poll-url="{{ route('conversaciones.poll', $conversacion) }}"
                         data-last-id="{{ $ultimoId }}"
                         data-poll-seconds="{{ config('campushop.chat_poll_seconds') }}">
                        @forelse ($mensajes as $mensaje)
                            @php $mio = $mensaje->emisor_id === auth()->id(); @endphp
                            <div class="d-flex {{ $mio ? 'justify-content-end' : 'justify-content-start' }} mb-3">
                                <div class="chat-bubble {{ $mio ? 'mine' : 'theirs' }}">
                                    {{ $mensaje->contenido }}
                                    <div class="time">{{ $mensaje->created_at->format('H:i') }}</div>
                                </div>
                            </div>
                        @empty
                            <p class="text-secondary text-center my-4">Aún no hay mensajes. ¡Saluda!</p>
                        @endforelse
                    </div>

                    <form method="POST" action="{{ route('conversaciones.mensajes', $conversacion) }}" class="d-flex gap-2">
                        @csrf
                        <input type="text" name="contenido" maxlength="1000" required autocomplete="off"
                               class="form-control @error('contenido') is-invalid @enderror" placeholder="Escribe un mensaje…">
                        <button class="btn btn-primary"><i class="bi bi-send"></i></button>
                    </form>
                    @error('contenido')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>
    </div>
@endsection
