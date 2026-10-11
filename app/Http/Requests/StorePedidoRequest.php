<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePedidoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * El carrito vive en la sesión (RFD-06). Desde el formulario solo se envían
     * método de pago y nota; los ítems, cantidades y precios se resuelven en el
     * servidor leyendo la base de datos (RNF-16).
     */
    public function rules(): array
    {
        return [
            'metodo_pago' => ['required', Rule::in(['efectivo', 'transferencia'])],
            'nota' => ['nullable', 'string', 'max:300'],
        ];
    }

    public function messages(): array
    {
        return [
            'metodo_pago.required' => 'Selecciona un método de pago.',
            'metodo_pago.in' => 'El método de pago no es válido.',
        ];
    }
}
