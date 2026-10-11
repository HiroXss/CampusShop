<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEmprendimientoRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Se autoriza con la Policy EmprendimientoPolicy::update en el controlador.
        return true;
    }

    public function rules(): array
    {
        $maxKb = (int) config('campushop.max_image_size_kb');
        $emprendimientoId = optional($this->user()->emprendimiento)->id;

        return [
            'nombre' => ['required', 'string', 'max:80', Rule::unique('emprendimientos', 'nombre')->ignore($emprendimientoId)],
            'descripcion' => ['required', 'string'],
            'categoria_id' => ['required', 'exists:categorias,id'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', "max:{$maxKb}"],
            'banner' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', "max:{$maxKb}"],
            'whatsapp' => ['nullable', 'string', 'max:20'],
            'instagram' => ['nullable', 'string', 'max:60'],
        ];
    }
}
