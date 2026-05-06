@php
    $resultJson = json_encode($record->result_preview ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    $sampleRecords = $record->result_preview['sample_records'] ?? [];
    $columns = collect($sampleRecords)->flatMap(fn($r) => array_keys((array) $r))->unique()->values()->all();
@endphp

<div class="space-y-4 text-sm" x-data="{
    activeTab: 'json',
    copied: false,
    resultJson: {{ Js::from($resultJson) }},
    copyResult() {
        navigator.clipboard.writeText(this.resultJson).then(() => {
            this.copied = true;
            setTimeout(() => { this.copied = false; }, 2000);
        });
    }
}">
    {{-- Meta info --}}
    <div class="grid grid-cols-2 gap-3 rounded-md border border-gray-200 bg-gray-50 p-3 dark:border-gray-700 dark:bg-gray-800/50">
        <div>
            <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Estado</p>
            <p class="mt-0.5 font-medium {{ $record->status === 'success' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                {{ $record->status }}
            </p>
        </div>
        <div>
            <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Registros</p>
            <p class="mt-0.5 font-medium text-gray-900 dark:text-white">{{ $record->records_count }}</p>
        </div>
        <div>
            <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Duración (ms)</p>
            <p class="mt-0.5 font-medium text-gray-900 dark:text-white">{{ $record->duration_ms ?? '-' }}</p>
        </div>
        <div>
            <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">LIMIT</p>
            <p class="mt-0.5 font-medium text-gray-900 dark:text-white">{{ $record->limit_value ?? '-' }}</p>
        </div>
    </div>

    {{-- Query --}}
    <div>
        <p class="mb-1 text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Consulta</p>
        <pre class="whitespace-pre-wrap rounded-md bg-gray-100 p-3 text-xs dark:bg-gray-900">{{ $record->soql }}</pre>
    </div>

    @if(filled($record->error_message))
        <div>
            <p class="mb-1 text-xs font-medium uppercase tracking-wide text-red-600 dark:text-red-400">Error</p>
            <pre class="whitespace-pre-wrap rounded-md bg-red-50 p-3 text-xs text-red-700 dark:bg-red-950 dark:text-red-300">{{ $record->error_message }}</pre>
        </div>
    @endif

    {{-- Tabs + copy button --}}
    <div>
        <div class="flex items-center justify-between border-b border-gray-200 dark:border-gray-700">
            <div class="flex gap-0">
                @foreach(['json' => 'JSON', 'table' => 'Tabla', 'tree' => 'Árbol'] as $tab => $label)
                    <button
                        type="button"
                        @click="activeTab = '{{ $tab }}'"
                        :class="activeTab === '{{ $tab }}'
                            ? 'border-b-2 border-primary-600 text-primary-600 dark:border-primary-400 dark:text-primary-400'
                            : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200'"
                        class="px-4 py-2 text-xs font-medium transition-colors"
                    >
                        {{ $label }}
                    </button>
                @endforeach
            </div>
            <button
                type="button"
                @click="copyResult()"
                class="mb-1 flex items-center gap-1.5 rounded-md border border-gray-300 bg-white px-3 py-1.5 text-xs font-medium text-gray-700 shadow-sm transition hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700"
            >
                <template x-if="!copied">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                </template>
                <template x-if="copied">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                </template>
                <span x-text="copied ? 'Copiado' : 'Copiar resultado'"></span>
            </button>
        </div>

        {{-- JSON tab --}}
        <div x-show="activeTab === 'json'" class="mt-3">
            <pre class="max-h-96 overflow-auto whitespace-pre-wrap rounded-md bg-gray-100 p-3 text-xs leading-relaxed dark:bg-gray-900">{{ $resultJson }}</pre>
        </div>

        {{-- Table tab --}}
        <div x-show="activeTab === 'table'" class="mt-3">
            @if(count($sampleRecords) > 0 && count($columns) > 0)
                <div class="max-h-96 overflow-auto rounded-md border border-gray-200 dark:border-gray-700">
                    <table class="w-full min-w-full divide-y divide-gray-200 text-xs dark:divide-gray-700">
                        <thead class="sticky top-0 bg-gray-50 dark:bg-gray-800">
                            <tr>
                                @foreach($columns as $col)
                                    <th class="whitespace-nowrap px-3 py-2 text-left font-medium uppercase tracking-wide text-gray-600 dark:text-gray-300">
                                        {{ $col }}
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white dark:divide-gray-700/50 dark:bg-gray-900">
                            @foreach($sampleRecords as $row)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                    @foreach($columns as $col)
                                        @php $cell = data_get($row, $col); @endphp
                                        <td class="px-3 py-2 text-gray-700 dark:text-gray-300">
                                            @if(is_array($cell) || is_object($cell))
                                                <span class="italic text-gray-400">[objeto]</span>
                                            @elseif(is_null($cell))
                                                <span class="italic text-gray-300 dark:text-gray-600">null</span>
                                            @else
                                                {{ $cell }}
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($record->records_count > count($sampleRecords))
                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                        Mostrando {{ count($sampleRecords) }} de {{ $record->records_count }} registros (preview).
                    </p>
                @endif
            @else
                <p class="rounded-md bg-gray-50 p-4 text-center text-gray-500 dark:bg-gray-800 dark:text-gray-400">
                    Sin registros para mostrar en tabla.
                </p>
            @endif
        </div>

        {{-- Tree tab --}}
        <div x-show="activeTab === 'tree'" class="mt-3">
            @if(count($sampleRecords) > 0)
                <div class="max-h-96 space-y-2 overflow-auto">
                    @foreach($sampleRecords as $index => $row)
                        <div
                            x-data="{ open: {{ $index === 0 ? 'true' : 'false' }} }"
                            class="rounded-md border border-gray-200 dark:border-gray-700"
                        >
                            <button
                                type="button"
                                @click="open = !open"
                                class="flex w-full items-center gap-2 px-3 py-2 text-left text-xs font-medium text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-800"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-3.5 w-3.5 transition-transform"
                                    :class="open ? 'rotate-90' : ''"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                </svg>
                                Registro {{ $index + 1 }}
                                @if(isset($row['Id']) || isset($row['id']))
                                    <span class="ml-1 font-mono text-gray-400">— {{ $row['Id'] ?? $row['id'] }}</span>
                                @endif
                            </button>
                            <div x-show="open" class="border-t border-gray-100 px-3 pb-2 dark:border-gray-700/50">
                                <dl class="mt-2 space-y-1">
                                    @foreach((array) $row as $key => $value)
                                        <div class="grid grid-cols-3 gap-2">
                                            <dt class="truncate font-medium text-gray-600 dark:text-gray-400">{{ $key }}</dt>
                                            <dd class="col-span-2 break-all text-gray-800 dark:text-gray-200">
                                                @if(is_array($value) || is_object($value))
                                                    <pre class="whitespace-pre-wrap rounded bg-gray-100 p-1 text-[10px] dark:bg-gray-900">{{ json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                                @elseif(is_null($value))
                                                    <span class="italic text-gray-300 dark:text-gray-600">null</span>
                                                @elseif(is_bool($value))
                                                    <span class="{{ $value ? 'text-green-600 dark:text-green-400' : 'text-red-500 dark:text-red-400' }}">{{ $value ? 'true' : 'false' }}</span>
                                                @else
                                                    {{ $value }}
                                                @endif
                                            </dd>
                                        </div>
                                    @endforeach
                                </dl>
                            </div>
                        </div>
                    @endforeach
                </div>
                @if($record->records_count > count($sampleRecords))
                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                        Mostrando {{ count($sampleRecords) }} de {{ $record->records_count }} registros (preview).
                    </p>
                @endif
            @else
                <p class="rounded-md bg-gray-50 p-4 text-center text-gray-500 dark:bg-gray-800 dark:text-gray-400">
                    Sin registros para mostrar en árbol.
                </p>
            @endif
        </div>
    </div>
</div>
