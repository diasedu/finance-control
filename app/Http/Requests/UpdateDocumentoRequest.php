<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDocumentoRequest extends FormRequest
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
            'valor' => ['nullable', 'numeric', 'min:0'],
            'data_documento' => ['nullable', 'date'],
            'fornecedor' => ['nullable', 'string', 'max:255'],
            'pessoa' => ['nullable', 'string', 'max:255'],
            'observacoes' => ['nullable', 'string', 'max:2000'],
            'arquivo' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
        ];
    }

    public function messages(): array
    {
        return [
            'arquivo.mimes' => 'O comprovante precisa ser um arquivo PDF.',
            'arquivo.max' => 'O arquivo não pode ultrapassar 10MB.',
        ];
    }
}
