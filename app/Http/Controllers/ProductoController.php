<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductoRequest;
use App\Http\Requests\UpdateProductoRequest;
use App\Models\Categoria;
use App\Models\Producto;
use App\Models\ProductoImagen;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProductoController extends Controller
{
    /**
     * Listado de productos del emprendedor (Mi tienda, RF-19).
     */
    public function index(Request $request): View
    {
        $emprendimiento = $request->user()->emprendimiento;

        $productos = $emprendimiento->productos()
            ->with('imagenes', 'categoria')
            ->latest()
            ->paginate(10);

        return view('mi-tienda.index', [
            'emprendimiento' => $emprendimiento,
            'productos' => $productos,
        ]);
    }

    public function create(): View
    {
        return view('mi-tienda.productos.crear', [
            'categorias' => Categoria::orderBy('nombre')->get(),
            'maxImagenes' => (int) config('campushop.max_images_per_product'),
        ]);
    }

    /**
     * Crear producto o servicio con 1..N imágenes (RF-15 a RF-19).
     */
    public function store(StoreProductoRequest $request): RedirectResponse
    {
        $emprendimiento = $request->user()->emprendimiento;
        abort_unless($emprendimiento, 403);

        $data = $request->validated();

        $producto = $emprendimiento->productos()->create([
            'categoria_id' => $data['categoria_id'],
            'nombre' => $data['nombre'],
            'descripcion' => $data['descripcion'],
            'tipo' => $data['tipo'],
            'precio' => $data['precio'],
            'stock' => $data['stock'] ?? null,
            'estado' => ($data['stock'] ?? null) === 0 ? Producto::ESTADO_AGOTADO : Producto::ESTADO_DISPONIBLE,
        ]);

        $this->guardarImagenes($producto, $request->file('imagenes'));

        return redirect()->route('mi-tienda.index')->with('success', 'Producto publicado correctamente.');
    }

    public function edit(Request $request, Producto $producto): View
    {
        $this->authorize('update', $producto);

        return view('mi-tienda.productos.editar', [
            'producto' => $producto->load('imagenes'),
            'categorias' => Categoria::orderBy('nombre')->get(),
            'maxImagenes' => (int) config('campushop.max_images_per_product'),
        ]);
    }

    /**
     * Actualizar producto, añadir o quitar imágenes (RF-20).
     */
    public function update(UpdateProductoRequest $request, Producto $producto): RedirectResponse
    {
        $this->authorize('update', $producto);

        $data = $request->validated();

        $producto->fill([
            'categoria_id' => $data['categoria_id'],
            'nombre' => $data['nombre'],
            'descripcion' => $data['descripcion'],
            'tipo' => $data['tipo'],
            'precio' => $data['precio'],
            'stock' => $data['stock'] ?? null,
        ]);

        // Recalcula el estado según el stock (RN-03).
        if ($producto->estado !== Producto::ESTADO_INACTIVO) {
            $producto->estado = ($producto->stock !== null && $producto->stock <= 0)
                ? Producto::ESTADO_AGOTADO
                : Producto::ESTADO_DISPONIBLE;
        }

        $producto->save();

        // Elimina las imágenes marcadas para quitar en el formulario (RF-20).
        $quitar = array_filter((array) $request->input('imagenes_quitar', []));
        if (! empty($quitar)) {
            $imagenes = ProductoImagen::where('producto_id', $producto->id)
                ->whereIn('id', $quitar)
                ->get();
            foreach ($imagenes as $imagen) {
                Storage::disk('public')->delete($imagen->ruta);
                $imagen->delete();
            }
        }

        if ($request->hasFile('imagenes')) {
            $this->guardarImagenes($producto, $request->file('imagenes'));
        }

        return redirect()->route('mi-tienda.index')->with('success', 'Producto actualizado.');
    }

    /**
     * Cambiar estado manualmente desde la tabla (RF-21, RF-22).
     */
    public function cambiarEstado(Request $request, Producto $producto): RedirectResponse
    {
        $this->authorize('update', $producto);

        $data = $request->validate([
            'estado' => ['required', 'in:disponible,inactivo,agotado'],
        ]);

        // No permitir marcar como disponible si está sin stock (RN-03).
        if ($data['estado'] === Producto::ESTADO_DISPONIBLE
            && $producto->stock !== null && $producto->stock <= 0) {
            return back()->with('error', 'No puedes activar un producto sin stock. Reabastece primero.');
        }

        $producto->estado = $data['estado'];
        $producto->save();

        return back()->with('success', "Producto marcado como «{$producto->estado}».");
    }

    /**
     * Eliminación lógica de un producto (RF-22, RN-10).
     */
    public function destroy(Producto $producto): RedirectResponse
    {
        $this->authorize('update', $producto);

        $producto->delete();

        return redirect()->route('mi-tienda.index')->with('success', 'Producto eliminado.');
    }

    /**
     * Guarda las imágenes respetando el máximo configurado (RN-02).
     */
    private function guardarImagenes(Producto $producto, ?array $archivos): void
    {
        if (empty($archivos)) {
            return;
        }

        $max = (int) config('campushop.max_images_per_product');
        $actuales = $producto->imagenes()->count();
        $orden = (int) $producto->imagenes()->max('orden');

        foreach ($archivos as $archivo) {
            if ($actuales >= $max) {
                break;
            }
            $ruta = $archivo->store('productos', 'public');
            ProductoImagen::create([
                'producto_id' => $producto->id,
                'ruta' => $ruta,
                'orden' => ++$orden,
            ]);
            $actuales++;
        }
    }
}
