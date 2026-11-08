<?php

namespace App\Models;

use App\Enums\TipoLancamento;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Um item/lançamento dentro de um comprovante (mês, ano, descrição,
 * valor, classificação e tipo entrada/saída).
 *
 * Igual ao Documento: só mapeamento e relação. Regra fica nos Services.
 */
class ItemDocumento extends Model
{
    use HasFactory;

    protected $table = 'itens_documento';

    protected $fillable = [
        'documento_id',
        'mes',
        'ano',
        'item',
        'valor',
        'classificacao',
        'tipo',
    ];

    protected function casts(): array
    {
        return [
            'mes' => 'integer',
            'ano' => 'integer',
            'valor' => 'decimal:2',
            'tipo' => TipoLancamento::class,
        ];
    }

    public function documento(): BelongsTo
    {
        return $this->belongsTo(Documento::class);
    }
}
