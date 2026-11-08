<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Um comprovante (documento) pode ter vários itens/lançamentos.
     * Cada item carrega o que será usado no dashboard de gastos:
     * mês, ano, descrição, valor, classificação e tipo (entrada/saída).
     */
    public function up(): void
    {
        Schema::create('itens_documento', function (Blueprint $table) {
            $table->id();

            $table->foreignId('documento_id')
                ->constrained('documentos')
                ->cascadeOnDelete();

            $table->unsignedTinyInteger('mes');
            $table->unsignedSmallInteger('ano');
            $table->string('item');
            $table->decimal('valor', 12, 2);
            $table->string('classificacao')->nullable();
            $table->string('tipo');

            $table->timestamps();

            $table->index(['ano', 'mes']);
            $table->index('tipo');
            $table->index('classificacao');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('itens_documento');
    }
};
