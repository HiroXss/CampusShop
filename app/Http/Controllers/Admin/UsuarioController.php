<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UsuarioController extends Controller
{
    /**
     * Gestión de cuentas (RF-56): activar/desactivar y eliminar.
     */
    public function index(Request $request): View
    {
        $query = User::with('emprendimiento');

        if ($request->input('q')) {
            $query->where(function ($w) use ($request) {
                $w->where('name', 'like', '%'.$request->input('q').'%')
                    ->orWhere('email', 'like', '%'.$request->input('q').'%');
            });
        }
        if ($request->input('rol') === 'admin') {
            $query->where('role', User::ROLE_ADMIN);
        } elseif ($request->input('rol') === 'user') {
            $query->where('role', User::ROLE_USER);
        }

        $usuarios = $query->latest()->paginate(15)->withQueryString();

        return view('admin.usuarios.index', compact('usuarios'));
    }

    /**
     * Activar o desactivar una cuenta (RF-05, bloqueo de cuentas desactivadas).
     */
    public function toggleActivo(User $usuario): RedirectResponse
    {
        if ($usuario->id === auth()->id()) {
            return back()->with('error', 'No puedes desactivar tu propia cuenta.');
        }

        $usuario->update(['is_active' => ! $usuario->is_active]);

        return back()->with('success', $usuario->is_active
            ? "Cuenta de {$usuario->name} activada."
            : "Cuenta de {$usuario->name} desactivada.");
    }

    /**
     * Eliminación lógica de una cuenta (RF-56).
     */
    public function destroy(User $usuario): RedirectResponse
    {
        if ($usuario->id === auth()->id()) {
            return back()->with('error', 'No puedes eliminar tu propia cuenta.');
        }

        $usuario->delete();

        return back()->with('success', "Cuenta de {$usuario->name} eliminada.");
    }
}