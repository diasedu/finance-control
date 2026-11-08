<?php

namespace App\Enums;

/**
 * Natureza do lançamento de um item de comprovante: entrada (receita)
 * ou saída (despesa). Usado depois para somatórios no dashboard de
 * gastos.
 */
enum TipoLancamento: string
{
    case Entrada = 'entrada';
    case Saida = 'saida';

    public function label(): string
    {
        return match ($this) {
            self::Entrada => 'Entrada',
            self::Saida => 'Saída',
        };
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::Entrada => 'bg-emerald-100 text-emerald-700 ring-emerald-300 dark:bg-emerald-500/15 dark:text-emerald-400 dark:ring-emerald-500/30',
            self::Saida => 'bg-rose-100 text-rose-700 ring-rose-300 dark:bg-rose-500/15 dark:text-rose-400 dark:ring-rose-500/30',
        };
    }
}
