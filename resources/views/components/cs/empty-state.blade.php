@props(['icon' => 'bi-inbox', 'title' => 'No hay contenido', 'description' => null, 'action' => null])

<div class="empty-state">
    <div class="empty-icon"><i class="bi {{ $icon }}"></i></div>
    <h5>{{ $title }}</h5>
    @if ($description)
        <p class="mb-3 mx-auto" style="max-width: 420px">{{ $description }}</p>
    @endif
    @if ($action)
        <div>{{ $action }}</div>
    @elseif ($slot->isNotEmpty())
        <div>{{ $slot }}</div>
    @endif
</div>