<?php

namespace App\Services\Ocr;

/**
 * Contrato para o serviço de OCR/extração de metadados via LLM.
 *
 * Mantido como interface para permitir troca de provedor (Anthropic,
 * OpenAI, um serviço de OCR tradicional, etc.) sem tocar no
 * DocumentoService.
 */
interface OcrServiceInterface
{
    /**
     * Extrai metadados estruturados de um comprovante em PDF.
     *
     * @param  string  $caminhoAbsolutoPdf  Caminho absoluto do PDF em disco.
     */
    public function extrairMetadados(string $caminhoAbsolutoPdf): OcrExtractionResult;
}
