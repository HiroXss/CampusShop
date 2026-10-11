<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Mostrar el formulario de registro.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Registrar un usuario solo con correo del dominio universitario (RF-01, RN-01).
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $dominio = config('campushop.university_domain');

        $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => [
                'required', 'string', 'lowercase', 'email', 'max:150',
                'unique:'.User::class,
                // RN-01: solo correos del dominio universitario.
                'regex:/@'.preg_quote($dominio, '/').'$/i',
            ],
            'program' => ['required', 'string', 'max:120'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'email.regex' => "Debes registrarte con un correo que termine en @{$dominio} (RN-01).",
            'email.unique' => 'Ya existe una cuenta con ese correo.',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'program' => $request->program,
            'password' => Hash::make($request->password),
            'role' => User::ROLE_USER,
            'is_active' => true,
        ]);

        event(new Registered($user));

        // El usuario debe verificar su correo antes de operar (RF-02).
        Auth::login($user);

        return redirect(route('verification.notice', absolute: false));
    }
}
