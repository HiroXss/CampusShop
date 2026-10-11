<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMensajeRequest;
use App\Models\Conversacion;
use App\Models\Emprendimiento;
use App\Models\Mensaje;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ConversacionController extends Controller
{
    /**
     * Bandeja de conversaciones del usuario (RF-31): como comprador o como
     * emprendedor, con contador de no leídos.
     */
    public function index(Request $request): View
    {
        $userId = Auth::id();
        $emprendimiento = $request->user()->emprendimiento;

        $conversaciones = Conversacion::query()
            ->where(function ($q) use ($userId, $emprendimiento) {
                $q->where('comprador_id', $userId);
                if ($emprendimiento) {
                    $q->orWhere('emprendimiento_id', $emprendimiento->id);
                }
            })
            ->with(['emprendimiento', 'comprador', 'ultimoMensaje'])
            ->orderByDesc(
                Mensaje::select('created_at')
                    ->whereColumn('conversacion_id', 'conversaciones.id')
                    ->latest()
                    ->take(1)
            )
            ->get();

        // Añade el conteo de no leídos de cada conversación.
        $conversaciones->each(function (Conversacion $c) use ($userId) {
            $c->no_leidos = $c->mensajesNoLeidos($userId)->count();
        });

        return view('conversaciones.index', compact('conversaciones'));
    }

    /**
     * Iniciar (o retomar) una conversación con un emprendimiento (RF-29, RN-05).
     * Solo hay una conversación por (emprendimiento, comprador).
     */
    public function iniciar(Request $request, Emprendimiento $emprendimiento): RedirectResponse
    {
        if ($emprendimiento->user_id === Auth::id()) {
            return back()->with('error', 'No puedes iniciar una conversación contigo mismo.');
        }

        $conversacion = Conversacion::firstOrCreate(
            [
                'emprendimiento_id' => $emprendimiento->id,
                'comprador_id' => Auth::id(),
            ],
            [
                'producto_id' => $request->integer('producto_id') ?: null,
            ]
        );

        return redirect()->route('conversaciones.show', $conversacion);
    }

    /**
     * Ver la conversación y marcar como leídos los mensajes recibidos (RF-32).
     */
    public function show(Conversacion $conversacion): View
    {
        $this->authorize('ver', $conversacion);

        $this->marcarLeidos($conversacion, Auth::id());

        $conversacion->load('emprendimiento.user', 'comprador', 'producto');
        $mensajes = $conversacion->mensajes()->with('emisor')->oldest()->get();

        return view('conversaciones.show', compact('conversacion', 'mensajes'));
    }

    /**
     * Enviar un mensaje de hasta 1000 caracteres (RF-30, RF-34).
     */
    public function store(StoreMensajeRequest $request, Conversacion $conversacion): RedirectResponse
    {
        $this->authorize('escribir', $conversacion);

        $conversacion->mensajes()->create([
            'emisor_id' => Auth::id(),
            'contenido' => $request->string('contenido')->toString(),
        ]);

        return redirect()->route('conversaciones.show', $conversacion);
    }

    /**
     * Endpoint de polling: devuelve los mensajes nuevos (RF-33).
     */
    public function poll(Request $request, Conversacion $conversacion): JsonResponse
    {
        $this->authorize('ver', $conversacion);

        $ultimo = (int) $request->input('ultimo', 0);

        $nuevos = $conversacion->mensajes()
            ->with('emisor')
            ->where('id', '>', $ultimo)
            ->oldest('id')
            ->get();

        $this->marcarLeidos($conversacion, Auth::id());

        return response()->json([
            'mensajes' => $nuevos->map(fn (Mensaje $m) => [
                'id' => $m->id,
                'mine' => $m->emisor_id === Auth::id(),
                'contenido' => e($m->contenido),
                'hora' => $m->created_at->format('H:i'),
            ]),
            'ultimo' => (int) ($nuevos->last()->id ?? $ultimo),
        ]);
    }

    /**
     * Total de mensajes no leídos (contador del navbar, RF-31).
     */
    public function noLeidos(Request $request): JsonResponse
    {
        $userId = Auth::id();
        $emprendimiento = $request->user()->emprendimiento;

        $total = Mensaje::whereNull('leido_at')
            ->where('emisor_id', '!=', $userId)
            ->whereHas('conversacion', function ($q) use ($userId, $emprendimiento) {
                $q->where('comprador_id', $userId);
                if ($emprendimiento) {
                    $q->orWhere('emprendimiento_id', $emprendimiento->id);
                }
            })
            ->count();

        return response()->json(['no_leidos' => $total]);
    }

    private function marcarLeidos(Conversacion $conversacion, int $userId): void
    {
        $conversacion->mensajes()
            ->where('emisor_id', '!=', $userId)
            ->whereNull('leido_at')
            ->update(['leido_at' => now()]);
    }
}