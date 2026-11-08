<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDocumentoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'titulo' => ['nullable', 'string', 'max:255'],
            'tipo' => ['nullable', 'string', 'max:100'],
            'pessoa' => ['nullable', 'string', 'max:255'],
            'observacoes' => ['nullable', 'string', 'max:2000'],
            'arquivo' => ['required', 'file', 'mimes:pdf', 'max:10240'],
        ];
    }

    public function messages(): array
    {
        return [
            'arquivo.required' => 'Selecione o arquivo PDF do comprovante.',
            'arquivo.mimes' => 'O comprovante precisa ser um arquivo PDF.',
            'arquivo.max' => 'O arquivo não pode ultrapassar 10MB.',
        ];
    }
}
