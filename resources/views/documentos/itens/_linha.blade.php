<tr
    data-item-id="{{ $item->id }}"
    data-mes="{{ $item->mes }}"
    data-ano="{{ $item->ano }}"
    data-item-nome="{{ $item->item }}"
    data-valor="{{ $item->valor }}"
    data-classificacao="{{ $item->classificacao }}"
    data-tipo="{{ $item->tipo->value }}"
    class="hover:bg-gray-50 dark:hover:bg-gray-800/40"
>
    <td class="px-4 py-2.5 text-gray-600 dark:text-gray-400">
        {{ sprintf('%02d/%d', $item->mes, $item->ano) }}
    </td>
    <td class="px-4 py-2.5 font-medium text-gray-900 dark:text-gray-100">{{ $item->item }}</td>
    <td class="px-4 py-2.5">
        <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium ring-1 ring-inset {{ $item->tipo->badgeClasses() }}">
            {{ $item->tipo->label() }}
        </span>
    </td>
    <td class="px-4 py-2.5 text-gray-600 dark:text-gray-400">{{ $item->classificacao ?: '—' }}</td>
    <td class="px-4 py-2.5 text-gray-900 dark:text-gray-100">
        R$ {{ number_format($item->valor, 2, ',', '.') }}
    </td>
    <td class="px-4 py-2.5 text-right">
        <div class="flex items-center justify-end gap-3 text-sm">
            <button type="button" class="editar-item text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100">
                Editar
            </button>
            <button type="button" class="excluir-item text-rose-600 hover:text-rose-800 dark:text-rose-400 dark:hover:text-rose-300">
                Excluir
            </button>
        </div>
    </td>
</tr>
