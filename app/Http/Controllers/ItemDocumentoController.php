<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreItemDocumentoRequest;
use App\Http\Requests\UpdateItemDocumentoRequest;
use App\Services\ItemDocumentoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * CRUD de itens de um documento, pensado para ser consumido via AJAX
 * pela tela de comprovante (sem recarregar a página). Quando a
 * requisição não pede JSON, cai de volta para um redirect normal.
 */
class ItemDocumentoController extends Controller
{
    public function __construct(
        private readonly ItemDocumentoService $itemDocumentoService,
    ) {
    }

    public function store(StoreItemDocumentoRequest $request, int $documento): JsonResponse|RedirectResponse
    {
        $item = $this->itemDocumentoService->adicionar($documento, $request->validated());

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'item' => $item,
                'html' => view('documentos.itens._linha', ['item' => $item])->render(),
            ], 201);
        }

        return redirect()
            ->route('documentos.show', $documento)
            ->with('success', 'Item adicionado.');
    }

    public function update(UpdateItemDocumentoRequest $request, int $item): JsonResponse|RedirectResponse
    {
        $atualizado = $this->itemDocumentoService->atualizar($item, $request->validated());

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'item' => $atualizado,
                'html' => view('documentos.itens._linha', ['item' => $atualizado])->render(),
            ]);
        }

        return redirect()
            ->route('documentos.show', $atualizado->documento_id)
            ->with('success', 'Item atualizado.');
    }

    public function destroy(Request $request, int $item): JsonResponse|RedirectResponse
    {
        $documentoId = $this->itemDocumentoService->buscar($item)->documento_id;

        $this->itemDocumentoService->excluir($item);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'id' => $item]);
        }

        return redirect()
            ->route('documentos.show', $documentoId)
            ->with('success', 'Item excluído.');
    }
}
