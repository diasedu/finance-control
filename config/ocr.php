<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Driver de OCR/extração via LLM
    |--------------------------------------------------------------------------
    |
    | anthropic_api: chama a API da Anthropic direto (Http + ANTHROPIC_API_KEY).
    | claude_cli: chama o Claude Code (`claude` no terminal) via subprocesso,
    |             usando o login de assinatura (Pro/Max) em vez de API key.
    |
    */
    'driver' => env('OCR_DRIVER', 'anthropic_api'),

    'claude_cli' => [
        'binary' => env('CLAUDE_CLI_BINARY', 'claude'),
        'timeout' => (int) env('CLAUDE_CLI_TIMEOUT', 180),
    ],

];
