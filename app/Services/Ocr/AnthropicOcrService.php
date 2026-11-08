<?php

namespace App\Services\Ocr;

use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Extrai metadados de um comprovante em PDF enviando o arquivo para a
 * API da Anthropic (Claude) e pedindo que a resposta volte em JSON.
 *
 * Esta classe só sabe conversar com a LLM e normalizar a resposta.
 * Toda a orquestração (onde salvar, o que fazer em caso de erro, qual
 * status atribuir) é responsabilidade do DocumentoService.
 */
class AnthropicOcrService implements OcrServiceInterface
{
    private const ENDPOINT = 'https://api.anthropic.com/v1/messages';

    private const PROMPT = <<<'PROMPT'
        Você é um extrator de dados de comprovantes financeiros (notas fiscais, recibos, boletos, faturas, extratos).

        Analise o PDF em anexo e devolva APENAS um JSON (sem markdown, sem texto antes ou depois) com exatamente estas chaves:
        {
          "tipo": "tipo do documento, ex: Nota Fiscal, Recibo, Boleto, Fatura, Extrato",
          "valor": "valor total do documento, apenas número com ponto decimal, sem símbolo de moeda",
          "data_documento": "data do documento no formato YYYY-MM-DD",
          "fornecedor": "nome do emissor, fornecedor ou prestador do serviço",
          "descricao": "um resumo curto (até 140 caracteres) do que foi pago/comprado",
          "itens": [
            {
              "mes": "mês do lançamento, número de 1 a 12",
              "ano": "ano do lançamento, 4 dígitos",
              "item": "descrição curta do item/lançamento",
              "valor": "valor desse item, apenas número com ponto decimal",
              "classificacao": "categoria do gasto, ex: Alimentação, Transporte, Moradia, Saúde, Salário, Serviços",
              "tipo": "entrada ou saida"
            }
          ]
        }

        Um comprovante pode ter mais de um item/lançamento (ex: várias linhas em um extrato ou em uma nota fiscal
        com múltiplos produtos): liste cada um separadamente no array "itens". Se houver apenas um, retorne um
        array com um único elemento. "tipo" de cada item é "entrada" quando é um recebimento/receita e "saida"
        quando é um pagamento/despesa.

        Seja conciso na "descricao" e no "item" de cada lançamento: isso evita gastar tokens desnecessários e
        cortar a resposta antes do JSON terminar. Se não conseguir identificar algum campo com confiança, use
        null para esse campo (exceto dentro de "itens", em que cada item deve sempre ter ao menos "item", "valor"
        e "tipo" preenchidos).
        PROMPT;

    public function __construct(
        private readonly ?string $apiKey,
        private readonly string $model,
        private readonly int $maxTokens = 8192,
    ) {
    }

    public function extrairMetadados(string $caminhoAbsolutoPdf): OcrExtractionResult
    {
        if (blank($this->apiKey)) {
            throw new RuntimeException(
                'ANTHROPIC_API_KEY não configurada. Defina a chave no .env para habilitar a extração via LLM.'
            );
        }

        if (! is_file($caminhoAbsolutoPdf)) {
            throw new RuntimeException("Arquivo não encontrado para processamento: {$caminhoAbsolutoPdf}");
        }

        $pdfBase64 = base64_encode(file_get_contents($caminhoAbsolutoPdf));

        $response = Http::withHeaders([
            'x-api-key' => $this->apiKey,
            'anthropic-version' => '2023-06-01',
            'content-type' => 'application/json',
        ])
            ->timeout(120)
            ->post(self::ENDPOINT, [
                'model' => $this->model,
                'max_tokens' => $this->maxTokens,
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => [
                            [
                                'type' => 'document',
                                'source' => [
                                    'type' => 'base64',
                                    'media_type' => 'application/pdf',
                                    'data' => $pdfBase64,
                                ],
                            ],
                            [
                                'type' => 'text',
                                'text' => self::PROMPT,
                            ],
                        ],
                    ],
                ],
            ]);

        if ($response->failed()) {
            throw new RuntimeException(
                'Falha ao chamar a API da Anthropic: '.$response->status().' '.$response->body()
            );
        }

        if ($response->json('stop_reason') === 'max_tokens') {
            throw new RuntimeException(
                'A resposta da LLM foi cortada por atingir o limite de tokens ('.$this->maxTokens.'). '.
                'Aumente ANTHROPIC_OCR_MAX_TOKENS no .env (comprovantes com muitos itens, como extratos '.
                'bancários longos, podem precisar de um limite maior) e reprocesse o documento.'
            );
        }

        $texto = collect($response->json('content', []))
            ->where('type', 'text')
            ->pluck('text')
            ->implode('');

        return OcrExtractionResult::fromArray($this->parseJson($texto));
    }

    private function parseJson(string $texto): array
    {
        $texto = trim($texto);

        // Remove eventuais blocos ```json ... ``` que a LLM insista em devolver.
        $texto = preg_replace('/^```(?:json)?|```$/m', '', $texto);
        $texto = trim($texto);

        $dados = json_decode($texto, true);

        if (! is_array($dados)) {
            throw new RuntimeException('A resposta da LLM não pôde ser interpretada como JSON: '.$texto);
        }

        return $dados;
    }
}
