<div class="space-y-4 text-sm">
    <div>
        <p class="font-medium text-gray-900 dark:text-white">Consulta</p>
        <pre class="mt-1 whitespace-pre-wrap rounded-md bg-gray-100 p-3 text-xs dark:bg-gray-900">{{ $record->soql }}</pre>
    </div>

    <div class="grid grid-cols-2 gap-3">
        <div>
            <p class="font-medium text-gray-900 dark:text-white">Estado</p>
            <p class="text-gray-700 dark:text-gray-300">{{ $record->status }}</p>
        </div>
        <div>
            <p class="font-medium text-gray-900 dark:text-white">Registros</p>
            <p class="text-gray-700 dark:text-gray-300">{{ $record->records_count }}</p>
        </div>
        <div>
            <p class="font-medium text-gray-900 dark:text-white">Duración (ms)</p>
            <p class="text-gray-700 dark:text-gray-300">{{ $record->duration_ms ?? '-' }}</p>
        </div>
        <div>
            <p class="font-medium text-gray-900 dark:text-white">LIMIT</p>
            <p class="text-gray-700 dark:text-gray-300">{{ $record->limit_value ?? '-' }}</p>
        </div>
    </div>

    @if(filled($record->error_message))
        <div>
            <p class="font-medium text-red-700 dark:text-red-300">Error</p>
            <pre class="mt-1 whitespace-pre-wrap rounded-md bg-red-50 p-3 text-xs text-red-700 dark:bg-red-950 dark:text-red-300">{{ $record->error_message }}</pre>
        </div>
    @endif

    <div>
        <p class="font-medium text-gray-900 dark:text-white">Resultado (preview)</p>
        <pre class="mt-1 whitespace-pre-wrap rounded-md bg-gray-100 p-3 text-xs dark:bg-gray-900">{{ json_encode($record->result_preview ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</pre>
    </div>
</div>
