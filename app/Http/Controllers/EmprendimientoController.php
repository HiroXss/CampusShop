<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEmprendimientoRequest;
use App\Http\Requests\UpdateEmprendimientoRequest;
use App\Models\Categoria;
use App\Models\Emprendimiento;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class EmprendimientoController extends Controller
{
    /**
     * Formulario para activar la tienda (RF-09).
     */
    public function crearForm(Request $request): View|RedirectResponse
    {
        if ($request->user()->emprendimiento) {
            return redirect()->route('mi-tienda.index');
        }

        return view('mi-emprendimiento.crear', [
            'categorias' => Categoria::orderBy('nombre')->get(),
        ]);
    }

    /**
     * Activa el emprendimiento en estado pendiente de aprobación (RF-09, RF-10, RN-02).
     */
    public function store(StoreEmprendimientoRequest $request): RedirectResponse
    {
        if ($request->user()->emprendimiento) {
            return redirect()->route('mi-tienda.index');
        }

        $data = $request->validated();

        $emprendimiento = new Emprendimiento();
        $emprendimiento->user_id = $request->user()->id;
        $emprendimiento->categoria_id = $data['categoria_id'];
        $emprendimiento->nombre = $data['nombre'];
        $emprendimiento->descripcion = $data['descripcion'];
        $emprendimiento->whatsapp = $data['whatsapp'] ?? null;
        $emprendimiento->instagram = $data['instagram'] ?? null;
        $emprendimiento->logo = $request->file('logo')->store('emprendimientos/logos', 'public');
        if ($request->hasFile('banner')) {
            $emprendimiento->banner = $request->file('banner')->store('emprendimientos/banners', 'public');
        }

        // Con REQUIRE_APPROVAL=false la tienda queda aprobada directamente (RN-02).
        $emprendimiento->estado = config('campushop.require_approval')
            ? Emprendimiento::ESTADO_PENDIENTE
            : Emprendimiento::ESTADO_APROBADO;

        $emprendimiento->save();

        $mensaje = $emprendimiento->estado === Emprendimiento::ESTADO_PENDIENTE
            ? 'Tu emprendimiento fue enviado y está pendiente de aprobación.'
            : '¡Tu tienda está lista! Ya puedes publicar productos.';

        return redirect()->route('mi-tienda.index')->with('success', $mensaje);
    }

    /**
     * Formulario de edición / estado del emprendimiento (RF-10, RF-11).
     */
    public function editar(Request $request): View
    {
        $emprendimiento = $request->user()->emprendimiento;
        abort_unless($emprendimiento, 404);

        return view('mi-emprendimiento.editar', [
            'emprendimiento' => $emprendimiento,
            'categorias' => Categoria::orderBy('nombre')->get(),
        ]);
    }

    /**
     * Actualizar datos (RF-10) y reenviar si estaba rechazado (RF-11).
     */
    public function update(UpdateEmprendimientoRequest $request): RedirectResponse
    {
        $emprendimiento = $request->user()->emprendimiento;
        abort_unless($emprendimiento, 404);

        $this->authorize('update', $emprendimiento);

        if ($emprendimiento->estado === Emprendimiento::ESTADO_SUSPENDIDO) {
            return back()->with('error', 'Tu emprendimiento está suspendido por un administrador.');
        }

        $emprendimiento->fill($request->only(['nombre', 'descripcion', 'categoria_id', 'whatsapp', 'instagram']));

        if ($request->hasFile('logo')) {
            Storage::disk('public')->delete($emprendimiento->logo);
            $emprendimiento->logo = $request->file('logo')->store('emprendimientos/logos', 'public');
        }
        if ($request->hasFile('banner')) {
            Storage::disk('public')->delete($emprendimiento->banner);
            $emprendimiento->banner = $request->file('banner')->store('emprendimientos/banners', 'public');
        }

        // Reenvío tras un rechazo: vuelve a quedar pendiente (RF-11).
        $reenviado = false;
        if ($emprendimiento->estado === Emprendimiento::ESTADO_RECHAZADO) {
            $emprendimiento->estado = Emprendimiento::ESTADO_PENDIENTE;
            $emprendimiento->motivo_rechazo = null;
            $emprendimiento->revisado_por = null;
            $emprendimiento->revisado_at = null;
            $reenviado = true;
        }

        $emprendimiento->save();

        return back()->with('success', $reenviado
            ? 'Tu emprendimiento fue reenviado para aprobación.'
            : 'Datos del emprendimiento actualizados.');
    }

    /**
     * Pausar la tienda (RF-12): deja de ser visible y de recibir pedidos.
     */
    public function pausar(Request $request): RedirectResponse
    {
        $emprendimiento = $request->user()->emprendimiento;
        abort_unless($emprendimiento, 404);
        $this->authorize('pausar', $emprendimiento);

        if ($emprendimiento->estado !== Emprendimiento::ESTADO_APROBADO) {
            return back()->with('error', 'Solo puedes pausar un emprendimiento aprobado.');
        }

        $emprendimiento->estado = Emprendimiento::ESTADO_PAUSADO;
        $emprendimiento->save();

        return back()->with('success', 'Tu tienda está pausada. No recibirá pedidos.');
    }

    /**
     * Reactivar la tienda (RF-12).
     */
    public function reactivar(Request $request): RedirectResponse
    {
        $emprendimiento = $request->user()->emprendimiento;
        abort_unless($emprendimiento, 404);

        $this->authorize('pausar', $emprendimiento);

        if ($emprendimiento->estado !== Emprendimiento::ESTADO_PAUSADO) {
            return back()->with('error', 'Tu emprendimiento no está pausado.');
        }

        $emprendimiento->estado = Emprendimiento::ESTADO_APROBADO;
        $emprendimiento->save();

        return back()->with('success', 'Tu tienda vuelve a estar activa.');
    }
}