<?php

namespace App\Providers;

use App\Models\Conversacion;
use App\Models\Emprendimiento;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\Resena;
use App\Policies\ConversacionPolicy;
use App\Policies\EmprendimientoPolicy;
use App\Policies\PedidoPolicy;
use App\Policies\ProductoPolicy;
use App\Policies\ResenaPolicy;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Relación polimórfica de reportes (RFD-09): 'producto' | 'emprendimiento'.
        \Illuminate\Database\Eloquent\Relations\Relation::enforceMorphMap([
            'producto' => Producto::class,
            'emprendimiento' => Emprendimiento::class,
        ]);

        // Policies (RNF-05).
        Gate::policy(Emprendimiento::class, EmprendimientoPolicy::class);
        Gate::policy(Producto::class, ProductoPolicy::class);
        Gate::policy(Pedido::class, PedidoPolicy::class);
        Gate::policy(Conversacion::class, ConversacionPolicy::class);
        Gate::policy(Resena::class, ResenaPolicy::class);

        // Paginación con Bootstrap 5 (RFD-05).
        Paginator::useBootstrapFive();

        // Rate limiting (RNF-13): login, pedidos y mensajes.
        RateLimiter::for('login', function (Request $request) {
            $clave = strtolower((string) $request->input('email')).'|'.$request->ip();

            return Limit::perMinute((int) config('campushop.max_login_attempts'))->by($clave);
        });

        RateLimiter::for('pedidos', function (Request $request) {
            return Limit::perHour((int) config('campushop.max_orders_per_hour'))->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('mensajes', function (Request $request) {
            return Limit::perMinute((int) config('campushop.max_messages_per_minute'))->by($request->user()?->id ?: $request->ip());
        });

        // Comparte el contador de emprendimientos pendientes con el dashboard admin.
        View::composer('layouts.dashboard', function ($view) {
            $pendientes = 0;
            if (auth()->check() && auth()->user()->isAdmin()) {
                $pendientes = Emprendimiento::where('estado', Emprendimiento::ESTADO_PENDIENTE)->count();
            }
            $view->with('pendientes', $pendientes);
        });
    }
}