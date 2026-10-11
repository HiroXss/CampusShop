<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmprendimientoRequest extends FormRequest
{
    public function authorize(): bool
    {
        // El dueño es el propio usuario; autorizado por middleware/auth en el controlador.
        return true;
    }

    public function rules(): array
    {
        $maxKb = (int) config('campushop.max_image_size_kb');

        return [
            'nombre' => ['required', 'string', 'max:80', 'unique:emprendimientos,nombre'],
            'descripcion' => ['required', 'string'],
            'categoria_id' => ['required', 'exists:categorias,id'],
            'logo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', "max:{$maxKb}"],
            'banner' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', "max:{$maxKb}"],
            'whatsapp' => ['nullable', 'string', 'max:20'],
            'instagram' => ['nullable', 'string', 'max:60'],
        ];
    }

    public function messages(): array
    {
        return [
            'logo.required' => 'El logo es obligatorio.',
            'logo.mimes' => 'El logo debe ser jpg, png o webp (RN-10).',
            'logo.max' => 'El logo no puede superar los 2 MB.',
            'nombre.unique' => 'Ya existe un emprendimiento con ese nombre (RN-02).',
        ];
    }
}
