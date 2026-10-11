<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CategoriaController extends Controller
{
    /**
     * CRUD de categorías (RF-52).
     */
    public function index(): View
    {
        $categorias = Categoria::withCount(['productos', 'emprendimientos'])->orderBy('nombre')->get();

        return view('admin.categorias.index', compact('categorias'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:80', 'unique:categorias,nombre'],
            'descripcion' => ['nullable', 'string', 'max:255'],
        ]);

        Categoria::create([
            'nombre' => $data['nombre'],
            'slug' => Str::slug($data['nombre']),
            'descripcion' => $data['descripcion'] ?? null,
        ]);

        return back()->with('success', 'Categoría creada.');
    }

    public function update(Request $request, Categoria $categoria): RedirectResponse
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:80', 'unique:categorias,nombre,'.$categoria->id],
            'descripcion' => ['nullable', 'string', 'max:255'],
        ]);

        if ($data['nombre'] !== $categoria->nombre) {
            $data['slug'] = Str::slug($data['nombre']);
        }

        $categoria->update($data);

        return back()->with('success', 'Categoría actualizada.');
    }

    /**
     * Eliminar categoría solo si no tiene productos ni emprendimientos (RF-52).
     */
    public function destroy(Categoria $categoria): RedirectResponse
    {
        if ($categoria->productos()->exists() || $categoria->emprendimientos()->exists()) {
            return back()->with('error', 'No puedes eliminar una categoría con productos o emprendimientos asociados.');
        }

        $categoria->delete();

        return back()->with('success', 'Categoría eliminada.');
    }
}