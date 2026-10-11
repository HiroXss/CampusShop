@props([
    'id' => 'modal-confirmar',
    'title' => '¿Confirmar acción?',
    'text' => 'Esta acción no se puede deshacer.',
    'action' => null,
    'btnLabel' => 'Confirmar',
    'btnClass' => 'btn-primary',
])

<div class="modal fade" id="{{ $id }}" tabindex="-1" aria-labelledby="{{ $id }}-label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="{{ $id }}-label">{{ $title }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">{{ $text }}</div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                @if ($action)
                    <form method="POST" action="{{ $action }}" class="d-inline" id="{{ $id }}-form">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn {{ $btnClass }}">{{ $btnLabel }}</button>
                    </form>
                @endif
                {{ $slot }}
            </div>
        </div>
    </div>
</div>