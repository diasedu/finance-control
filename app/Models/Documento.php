<?php

namespace App\Models;

use App\Enums\DocumentoStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Representa um comprovante/documento enviado pelo usuário.
 *
 * Propositalmente "burro": apenas mapeamento de atributos, casts e
 * configuração de persistência. Qualquer regra (upload, chamada de OCR,
 * transição de status, exclusão de arquivo em disco etc.) fica nos
 * Services, nunca aqui.
 */
class Documento extends Model
{
    use HasFactory;

    protected $table = 'documentos';

    protected $fillable = [
        'titulo',
        'arquivo_path',
        'arquivo_nome_original',
        'arquivo_mime',
        'arquivo_tamanho',
        'tipo',
        'valor',
        'data_documento',
        'fornecedor',
        'pessoa',
        'observacoes',
        'status',
        'dados_extraidos',
        'erro_processamento',
    ];

    protected function casts(): array
    {
        return [
            'valor' => 'decimal:2',
            'data_documento' => 'date',
            'dados_extraidos' => 'array',
            'status' => DocumentoStatus::class,
        ];
    }

    public function itensDocumento(): HasMany
    {
        return $this->hasMany(ItemDocumento::class);
    }
}
