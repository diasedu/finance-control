<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Estrutura provisória para o protótipo de tela. Os campos definitivos
     * (e eventuais relacionamentos, ex.: usuário responsável) serão
     * ajustados quando a modelagem final do banco for definida.
     */
    public function up(): void
    {
        Schema::create('documentos', function (Blueprint $table) {
            $table->id();

            // Dados do upload
            $table->string('titulo')->nullable();
            $table->string('arquivo_path');
            $table->string('arquivo_nome_original');
            $table->string('arquivo_mime')->nullable();
            $table->unsignedBigInteger('arquivo_tamanho')->nullable();

            // Metadados extraídos via OCR/LLM (ou informados manualmente)
            $table->string('tipo')->nullable();
            $table->decimal('valor', 12, 2)->nullable();
            $table->date('data_documento')->nullable();
            $table->string('fornecedor')->nullable();
            $table->text('observacoes')->nullable();

            // Controle do processamento de OCR
            $table->string('status')->default('pendente');
            $table->json('dados_extraidos')->nullable();
            $table->text('erro_processamento')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documentos');
    }
};
