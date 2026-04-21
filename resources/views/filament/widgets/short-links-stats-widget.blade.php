<x-filament::card>
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    🔗 Links Cortos
                </h3>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Estadísticas de tus URLs acortadas
                </p>
            </div>
        </div>

        <!-- Estadísticas en Grid -->
        <div class="grid grid-cols-4 gap-3">
            <div class="rounded-lg bg-blue-50 dark:bg-blue-900/20 p-3">
                <p class="text-xs font-medium text-blue-600 dark:text-blue-400 uppercase tracking-wider">Total</p>
                <p class="mt-1 text-2xl font-bold text-blue-900 dark:text-blue-100">{{ $this->totalLinks }}</p>
            </div>
            <div class="rounded-lg bg-green-50 dark:bg-green-900/20 p-3">
                <p class="text-xs font-medium text-green-600 dark:text-green-400 uppercase tracking-wider">Activos</p>
                <p class="mt-1 text-2xl font-bold text-green-900 dark:text-green-100">{{ $this->activeLinks }}</p>
            </div>
            <div class="rounded-lg bg-purple-50 dark:bg-purple-900/20 p-3">
                <p class="text-xs font-medium text-purple-600 dark:text-purple-400 uppercase tracking-wider">Visitas</p>
                <p class="mt-1 text-2xl font-bold text-purple-900 dark:text-purple-100">{{ $this->totalVisits }}</p>
            </div>
            <div class="rounded-lg bg-orange-50 dark:bg-orange-900/20 p-3">
                <p class="text-xs font-medium text-orange-600 dark:text-orange-400 uppercase tracking-wider">Últimos 7d</p>
                <p class="mt-1 text-2xl font-bold text-orange-900 dark:text-orange-100">{{ $this->visitsLast7Days }}</p>
            </div>
        </div>

        <!-- Enlace a Recursos -->
        <div class="flex gap-2">
            <a
                href="{{ $this->resourceUrl }}"
                class="flex-1 inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-center text-sm font-semibold text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors dark:focus:ring-offset-gray-900"
            >
                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.658 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                </svg>
                Ver todos los Links
            </a>
        </div>

        <!-- Información adicional -->
        <div class="rounded-lg bg-gray-50 dark:bg-gray-800/50 p-3 text-xs text-gray-600 dark:text-gray-400">
            <p>💡 Crea y gestiona links cortos con rastreo de visitas y configuración de Google Tag Manager personalizada.</p>
        </div>
    </div>
</x-filament::card>
