<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreReporteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tipo' => ['required', Rule::in(['producto', 'emprendimiento'])],
            'id' => ['required', 'integer'],
            'motivo' => ['required', 'string', 'max:300'],
        ];
    }

    public function messages(): array
    {
        return [
            'motivo.required' => 'Debes indicar un motivo.',
        ];
    }
}