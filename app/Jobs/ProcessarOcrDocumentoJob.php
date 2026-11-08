<?php

namespace App\Jobs;

use App\Services\DocumentoService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Dispara a extração via LLM (OCR) de um documento em background.
 *
 * Intencionalmente fino: só sabe "qual documento processar" e delega
 * toda a regra pro DocumentoService, igual um Controller faria.
 */
class ProcessarOcrDocumentoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;

    public int $timeout = 300;

    public function __construct(
        private readonly int $documentoId,
    ) {
    }

    public function handle(DocumentoService $documentoService): void
    {
        $documentoService->executarExtracaoOcr($this->documentoId);
    }
}
