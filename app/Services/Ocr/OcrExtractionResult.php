<?php

namespace App\Services\Ocr;

/**
 * DTO com o resultado da extração de metadados feita pela LLM a partir
 * do comprovante em PDF: os dados gerais do documento e a lista de
 * itens/lançamentos (um comprovante pode ter mais de um item).
 */
final class OcrExtractionResult
{
    /**
     * @param  array<int, ItemExtraido>  $itens
     */
    public function __construct(
        public readonly ?string $tipo,
        public readonly ?float $valor,
        public readonly ?string $dataDocumento,
        public readonly ?string $fornecedor,
        public readonly ?string $descricao,
        public readonly array $itens,
        public readonly array $bruto,
    ) {
    }

    public static function fromArray(array $dados): self
    {
        $itens = array_map(
            static fn (array $item) => ItemExtraido::fromArray($item),
            $dados['itens'] ?? [],
        );

        return new self(
            tipo: $dados['tipo'] ?? null,
            valor: isset($dados['valor']) ? (float) $dados['valor'] : null,
            dataDocumento: $dados['data_documento'] ?? null,
            fornecedor: $dados['fornecedor'] ?? null,
            descricao: $dados['descricao'] ?? null,
            itens: $itens,
            bruto: $dados,
        );
    }
}
