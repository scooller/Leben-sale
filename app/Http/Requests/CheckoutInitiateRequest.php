<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CheckoutInitiateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'plant_id' => ['required', 'integer', 'exists:plants,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'gateway' => ['required', 'string', 'in:transbank,mercadopago'],
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($this->user()?->id),
            ],
            'phone' => ['required', 'string', 'max:20'],
            'rut' => [
                'required',
                'string',
                'max:12',
                Rule::unique('users', 'rut')->ignore($this->user()?->id),
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'plant_id' => 'planta',
            'quantity' => 'cantidad',
            'gateway' => 'pasarela de pago',
            'name' => 'nombre completo',
            'email' => 'correo electronico',
            'phone' => 'telefono',
            'rut' => 'RUT',
        ];
    }
}
