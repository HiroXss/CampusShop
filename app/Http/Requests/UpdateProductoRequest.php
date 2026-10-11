<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $maxImagenes = (int) config('campushop.max_images_per_product');
        $maxKb = (int) config('campushop.max_image_size_kb');

        return [
            'nombre' => ['required', 'string', 'max:120'],
            'descripcion' => ['required', 'string'],
            'tipo' => ['required', Rule::in(['producto', 'servicio'])],
            'categoria_id' => ['required', 'exists:categorias,id'],
            'precio' => ['required', 'numeric', 'gt:0'],
            'stock' => ['nullable', 'integer', 'min:0'],
            // Al editar, las imágenes existentes se conservan; se pueden añadir hasta el máximo.
            'imagenes' => ['nullable', 'array', "max:{$maxImagenes}"],
            'imagenes.*' => ['image', 'mimes:jpg,jpeg,png,webp', "max:{$maxKb}"],
        ];
    }

    public function messages(): array
    {
        return [
            'imagenes.max' => 'Máximo '.config('campushop.max_images_per_product').' imágenes por producto.',
            'imagenes.*.mimes' => 'Las imágenes deben ser jpg, png o webp (RN-10).',
            'imagenes.*.max' => 'Cada imagen debe pesar máximo 2 MB.',
            'precio.gt' => 'El precio debe ser mayor que 0.',
        ];
    }
}