<?php

namespace App\Services\Ocr;

use Illuminate\Support\Facades\Process;
use RuntimeException;
use Throwable;

/**
 * Extrai metadados de um comprovante em PDF usando o Claude Code CLI
 * (`claude -p ...`) como subprocesso, autenticado pelo login de
 * assinatura (Pro/Max) em vez de uma API key.
 *
 * Roda num diretório temporário isolado — nunca na raiz do projeto —
 * para não carregar hooks, plugins, MCP servers ou o CLAUDE.md do
 * repositório, e usa --allowedTools "Read" + --permission-mode default
 * + --permission-prompts none para garantir que nenhuma chamada fique
 * esperando uma aprovação que nunca vai chegar (o processo roda sem
 * ninguém olhando). Como o comprovante é um PDF enviado por um
 * usuário, só a leitura do arquivo é permitida: nada de Bash, Write
 * ou Edit, então mesmo uma instrução maliciosa escondida no PDF não
 * tem como executar nada além de ser lida como texto.
 */
class ClaudeCliOcrService implements OcrServiceInterface
{
    private const NOME_ARQUIVO_NA_SANDBOX = 'comprovante.pdf';

    private const JSON_SCHEMA = <<<'JSON'
        {
          "type": "object",
          "properties": {
            "tipo": {"type": ["string", "null"]},
            "valor": {"type": ["number", "null"]},
            "data_documento": {"type": ["string", "null"]},
            "fornecedor": {"type": ["string", "null"]},
            "descricao": {"type": ["string", "null"]},
            "itens": {
              "type": "array",
              "items": {
                "type": "object",
                "properties": {
                  "mes": {"type": "integer"},
                  "ano": {"type": "integer"},
                  "item": {"type": "string"},
                  "valor": {"type": "number"},
                  "classificacao": {"type": ["string", "null"]},
                  "tipo": {"type": "string", "enum": ["entrada", "saida"]}
                },
                "required": ["mes", "ano", "item", "valor", "tipo"]
              }
            }
          },
          "required": ["itens"]
        }
        JSON;

    public function __construct(
        private readonly string $binario,
        private readonly int $timeoutSegundos,
    ) {
    }

    public function extrairMetadados(string $caminhoAbsolutoPdf): OcrExtractionResult
    {
        if (! is_file($caminhoAbsolutoPdf)) {
            throw new RuntimeException("Arquivo não encontrado para processamento: {$caminhoAbsolutoPdf}");
        }

        $sandbox = $this->criarSandbox($caminhoAbsolutoPdf);

        try {
            $resultado = Process::path($sandbox)
                ->timeout($this->timeoutSegundos)
                ->run([
                    $this->binario,
                    '-p', $this->montarPrompt(),
                    '--output-format', 'json',
                    '--json-schema', self::JSON_SCHEMA,
                    '--allowedTools', 'Read',
                    '--permission-mode', 'default',
                    '--permission-prompts', 'none',
                ]);

            if ($resultado->failed()) {
                throw new RuntimeException(
                    'Falha ao executar o Claude Code CLI (código '.$resultado->exitCode().'): '.
                    trim($resultado->errorOutput().' '.$resultado->output())
                );
            }

            return OcrExtractionResult::fromArray($this->extrairStructuredOutput($resultado->output()));
        } finally {
            $this->limparSandbox($sandbox);
        }
    }

    private function montarPrompt(): string
    {
        return 'Leia o arquivo '.self::NOME_ARQUIVO_NA_SANDBOX.' nesta pasta: é um comprovante financeiro '.
            '(nota fiscal, recibo, boleto, fatura ou extrato). Extraia: tipo do documento, valor total, '.
            'data do documento (YYYY-MM-DD), fornecedor/emissor e uma descrição curta (até 140 caracteres). '.
            'Extraia também a lista de itens/lançamentos do comprovante — pode haver mais de um (ex: várias '.
            'linhas de um extrato ou produtos de uma nota fiscal). Para cada item informe: mês (1-12), ano '.
            '(4 dígitos), descrição curta, valor, classificação do gasto (ex: Alimentação, Transporte, '.
            'Moradia, Saúde, Salário, Serviços) e tipo ("entrada" para recebimento/receita, "saida" para '.
            'pagamento/despesa). Se não conseguir identificar um campo do documento com confiança, use null '.
            '(exceto dentro de cada item, que deve sempre ter mes, ano, item, valor e tipo preenchidos).';
    }

    private function criarSandbox(string $caminhoAbsolutoPdf): string
    {
        $sandbox = sys_get_temp_dir().DIRECTORY_SEPARATOR.'claude-ocr-'.bin2hex(random_bytes(8));

        if (! mkdir($sandbox, 0700, true) && ! is_dir($sandbox)) {
            throw new RuntimeException("Não foi possível criar o diretório temporário: {$sandbox}");
        }

        if (! copy($caminhoAbsolutoPdf, $sandbox.DIRECTORY_SEPARATOR.self::NOME_ARQUIVO_NA_SANDBOX)) {
            throw new RuntimeException('Não foi possível copiar o PDF para o diretório temporário de processamento.');
        }

        return $sandbox;
    }

    private function limparSandbox(string $sandbox): void
    {
        try {
            @unlink($sandbox.DIRECTORY_SEPARATOR.self::NOME_ARQUIVO_NA_SANDBOX);
            @rmdir($sandbox);
        } catch (Throwable) {
            // Falha ao limpar o diretório temporário não deve derrubar o processamento.
        }
    }

    private function extrairStructuredOutput(string $saidaBruta): array
    {
        $payload = json_decode(trim($saidaBruta), true);

        if (! is_array($payload)) {
            throw new RuntimeException('A saída do Claude Code CLI não pôde ser interpretada como JSON: '.$saidaBruta);
        }

        if (! isset($payload['structured_output']) || ! is_array($payload['structured_output'])) {
            throw new RuntimeException(
                'O Claude Code CLI não retornou "structured_output". Resposta: '.($payload['result'] ?? $saidaBruta)
            );
        }

        return $payload['structured_output'];
    }
}
