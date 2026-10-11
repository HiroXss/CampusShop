@php
    $esEdicion = $producto !== null;
@endphp

<div class="row">
    <div class="col-md-8 mb-3">
        <label class="form-label" for="nombre">Nombre</label>
        <input id="nombre" name="nombre" value="{{ old('nombre', $producto?->nombre) }}" required maxlength="120"
               class="form-control @error('nombre') is-invalid @enderror" placeholder="Ej: Torta de chocolate">
        @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label" for="tipo">Tipo</label>
        <select id="tipo" name="tipo" class="form-select @error('tipo') is-invalid @enderror" required>
            <option value="producto" @selected(old('tipo', $producto?->tipo) === 'producto')>Producto</option>
            <option value="servicio" @selected(old('tipo', $producto?->tipo) === 'servicio')>Servicio</option>
        </select>
        @error('tipo')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label" for="categoria_id">Categoría</label>
        <select id="categoria_id" name="categoria_id" class="form-select @error('categoria_id') is-invalid @enderror" required>
            <option value="">Selecciona…</option>
            @foreach ($categorias as $categoria)
                <option value="{{ $categoria->id }}" @selected(old('categoria_id', $producto?->categoria_id) == $categoria->id)>{{ $categoria->nombre }}</option>
            @endforeach
        </select>
        @error('categoria_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3 mb-3">
        <label class="form-label" for="precio">Precio (COP)</label>
        <input id="precio" type="number" min="0" step="100" name="precio" value="{{ old('precio', $producto?->precio) }}" required
               class="form-control @error('precio') is-invalid @enderror" placeholder="25000">
        @error('precio')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3 mb-3">
        <label class="form-label" for="stock">Stock</label>
        <input id="stock" type="number" min="0" name="stock" value="{{ old('stock', $producto?->stock) }}"
               class="form-control @error('stock') is-invalid @enderror" placeholder="Opcional">
        @error('stock')<div class="invalid-feedback">{{ $message }}</div>@enderror
        <div class="form-text">Déjalo vacío para productos con stock ilimitado o servicios.</div>
    </div>
</div>

<div class="mb-3">
    <label class="form-label" for="descripcion">Descripción</label>
    <textarea id="descripcion" name="descripcion" rows="4" required
              class="form-control @error('descripcion') is-invalid @enderror" placeholder="Describe tu producto o servicio…">{{ old('descripcion', $producto?->descripcion) }}</textarea>
    @error('descripcion')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

@if ($esEdicion && $producto->imagenes->isNotEmpty())
    <div class="mb-3">
        <label class="form-label">Imágenes actuales</label>
        <div class="d-flex flex-wrap gap-3">
            @foreach ($producto->imagenes as $imagen)
                <div class="text-center">
                    <img src="{{ Storage::url($imagen->ruta) }}" alt="" class="rounded-2" style="width:96px;height:96px;object-fit:cover;background:#E7EEFF;">
                    <div class="form-check mt-1">
                        <input class="form-check-input" type="checkbox" name="imagenes_quitar[]" value="{{ $imagen->id }}" id="quitar-{{ $imagen->id }}">
                        <label class="form-check-label small" for="quitar-{{ $imagen->id }}">Quitar</label>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif

<div class="mb-3">
    <label class="form-label" for="imagenes">{{ $esEdicion ? 'Agregar imágenes' : 'Imágenes' }} (1 a {{ $maxImagenes }})</label>
    <input id="imagenes" type="file" name="imagenes[]" accept="image/*" multiple
           @if (! $esEdicion) required @endif
           data-preview-multi="#imagenes-preview" data-max="{{ $maxImagenes }}"
           class="form-control @error('imagenes') is-invalid @enderror @error('imagenes.*') is-invalid @enderror">
    @error('imagenes')<div class="invalid-feedback">{{ $message }}</div>@enderror
    @error('imagenes.*')<div class="invalid-feedback">{{ $message }}</div>@enderror
    <div class="form-text">Formatos jpg, png o webp. Máximo {{ $maxImagenes }} imágenes de 2 MB cada una (RN-10).</div>
    <div id="imagenes-preview" class="mt-2 d-flex flex-wrap"></div>
</div>
