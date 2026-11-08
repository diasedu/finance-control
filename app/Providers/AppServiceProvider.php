<?php

namespace App\Providers;

use App\Repositories\DocumentoRepositoryInterface;
use App\Repositories\EloquentDocumentoRepository;
use App\Repositories\EloquentItemDocumentoRepository;
use App\Repositories\ItemDocumentoRepositoryInterface;
use App\Services\Ocr\AnthropicOcrService;
use App\Services\Ocr\ClaudeCliOcrService;
use App\Services\Ocr\OcrServiceInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(DocumentoRepositoryInterface::class, EloquentDocumentoRepository::class);
        $this->app->bind(ItemDocumentoRepositoryInterface::class, EloquentItemDocumentoRepository::class);

        $this->app->bind(OcrServiceInterface::class, function () {
            return match (config('ocr.driver')) {
                'claude_cli' => new ClaudeCliOcrService(
                    binario: config('ocr.claude_cli.binary'),
                    timeoutSegundos: config('ocr.claude_cli.timeout'),
                ),
                default => new AnthropicOcrService(
                    apiKey: config('services.anthropic.api_key'),
                    model: config('services.anthropic.ocr_model'),
                    maxTokens: (int) config('services.anthropic.ocr_max_tokens', 8192),
                ),
            };
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
