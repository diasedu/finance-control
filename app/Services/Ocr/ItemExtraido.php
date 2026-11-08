<?php

namespace App\Services\Ocr;

/**
 * Um item/lançamento identificado pela LLM dentro do comprovante.
 */
final class ItemExtraido
{
    public function __construct(
        public readonly ?int $mes,
        public readonly ?int $ano,
        public readonly ?string $item,
        public readonly ?float $valor,
        public readonly ?string $classificacao,
        public readonly ?string $tipo,
    ) {
    }

    public static function fromArray(array $dados): self
    {
        return new self(
            mes: isset($dados['mes']) ? (int) $dados['mes'] : null,
            ano: isset($dados['ano']) ? (int) $dados['ano'] : null,
            item: $dados['item'] ?? null,
            valor: isset($dados['valor']) ? (float) $dados['valor'] : null,
            classificacao: $dados['classificacao'] ?? null,
            tipo: $dados['tipo'] ?? null,
        );
    }

    /**
     * Só é aproveitável se tiver o mínimo de informação para virar uma
     * linha de ItemDocumento (mes, ano, descrição, valor e tipo).
     */
    public function completo(): bool
    {
        return $this->mes !== null
            && $this->ano !== null
            && filled($this->item)
            && $this->valor !== null
            && in_array($this->tipo, ['entrada', 'saida'], true);
    }
}
