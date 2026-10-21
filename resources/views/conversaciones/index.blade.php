@extends('layouts.public')

@section('title', 'Mensajes')

@section('content')
    <div class="container py-4">
        <h1 class="h3 fw-bold mb-4"><i class="bi bi-chat-dots me-2"></i>Mensajes</h1>

        @if ($conversaciones->isEmpty())
            <x-cs.empty-state icon="bi-chat-square-text" title="No tienes conversaciones" description="Cuando contactes a un emprendimiento verás aquí tus mensajes.">
                <a href="{{ route('catalogo.index') }}" class="btn btn-primary">Explorar catálogo</a>
            </x-cs.empty-state>
        @else
            <div class="cs-card p-0 overflow-hidden">
                @foreach ($conversaciones as $conversacion)
                    @php
                        $soyComprador = auth()->id() === $conversacion->comprador_id;
                        $otro = $soyComprador ? $conversacion->emprendimiento : $conversacion->comprador;
                        $avatar = $soyComprador ? $conversacion->emprendimiento->logo : $conversacion->comprador->avatar;
                    @endphp
                    <a href="{{ route('conversaciones.show', $conversacion) }}" class="text-decoration-none d-flex align-items-center gap-3 p-3 {{ ! $loop->last ? 'border-bottom' : '' }}">
                        <img src="{{ $avatar ? imagen($avatar) : ($soyComprador ? asset('img/logo-placeholder.svg') : asset('img/avatar-placeholder.svg')) }}"
                             alt="" class="rounded-circle" style="width:48px;height:48px;object-fit:cover;">
                        <div class="flex-grow-1 min-w-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-semibold" style="color:var(--cs-text)">{{ $otro->name ?? $otro->nombre }}</span>
                                <span class="small text-secondary">{{ $conversacion->ultimoMensaje?->created_at?->diffForHumans() }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="small text-secondary text-truncate" style="max-width:70%">
                                    {{ $conversacion->ultimoMensaje ? \Illuminate\Support\Str::limit($conversacion->ultimoMensaje->contenido, 50) : 'Sin mensajes' }}
                                </span>
                                @if ($conversacion->no_leidos > 0)
                                    <span class="badge rounded-pill text-bg-primary">{{ $conversacion->no_leidos }}</span>
                                @endif
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
@endsection
