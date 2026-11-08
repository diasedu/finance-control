<?php

namespace App\Enums;

/**
 * Status do processamento de OCR/LLM de um documento.
 *
 * Os métodos aqui são apenas apresentação (rótulo e cor para a UI),
 * não regra de negócio: a transição de status é decidida pelo
 * DocumentoService.
 */
enum DocumentoStatus: string
{
    case Pendente = 'pendente';
    case Processando = 'processando';
    case Processado = 'processado';
    case Erro = 'erro';

    public function label(): string
    {
        return match ($this) {
            self::Pendente => 'Pendente',
            self::Processando => 'Processando',
            self::Processado => 'Processado',
            self::Erro => 'Erro no processamento',
        };
    }

    /**
     * Classes Tailwind para o badge de status.
     */
    public function badgeClasses(): string
    {
        return match ($this) {
            self::Pendente => 'bg-gray-100 text-gray-700 ring-gray-300 dark:bg-gray-500/15 dark:text-gray-300 dark:ring-gray-500/30',
            self::Processando => 'bg-amber-100 text-amber-700 ring-amber-300 dark:bg-amber-500/15 dark:text-amber-400 dark:ring-amber-500/30',
            self::Processado => 'bg-emerald-100 text-emerald-700 ring-emerald-300 dark:bg-emerald-500/15 dark:text-emerald-400 dark:ring-emerald-500/30',
            self::Erro => 'bg-rose-100 text-rose-700 ring-rose-300 dark:bg-rose-500/15 dark:text-rose-400 dark:ring-rose-500/30',
        };
    }
}
