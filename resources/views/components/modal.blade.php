@props(['title', 'text' => '', 'footer' => false, 'footerClasses' => 'd-flex justify-content-end gap-2'])

<div {{ $attributes->merge(['class' => 'modal fade']) }} tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">{{ $title }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                @if ($text) <p class="mb-0">{{ $text }}</p> @endif
                {{ $slot }}
            </div>
            @if ($footer)
                <div class="modal-footer border-0 pt-0 {{ $footerClasses }}">{{ $footer }}</div>
            @endif
        </div>
    </div>
</div>