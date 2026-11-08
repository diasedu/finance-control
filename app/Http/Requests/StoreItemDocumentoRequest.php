<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreItemDocumentoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'mes' => ['required', 'integer', 'between:1,12'],
            'ano' => ['required', 'integer', 'digits:4'],
            'item' => ['required', 'string', 'max:255'],
            'valor' => ['required', 'numeric', 'min:0'],
            'classificacao' => ['nullable', 'string', 'max:100'],
            'tipo' => ['required', 'in:entrada,saida'],
        ];
    }
}
