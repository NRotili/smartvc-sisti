<x-filament-widgets::widget>
    <x-filament::section heading="Cámaras con grabación anómala" icon="heroicon-o-video-camera-slash">
        <x-slot name="description">
            Funcionan según Digifort pero no escriben a disco (excluye cámaras en mantenimiento)
        </x-slot>

        @if(count($servidoresCaidos) > 0)
            <p class="text-sm text-warning-600 dark:text-warning-400 mb-3">
                Sin datos de: {{ implode(', ', $servidoresCaidos) }} (servidor no responde).
            </p>
        @endif

        @if(count($anomalas) === 0)
            <div style="display:flex; align-items:center; gap:0.5rem;" class="text-success-600 dark:text-success-400 py-2">
                <x-filament::icon icon="heroicon-o-check-circle" style="width:1.25rem; height:1.25rem;" />
                <span class="text-sm font-medium">Todas las cámaras operativas están grabando correctamente.</span>
            </div>
        @else
            <div style="overflow-x:auto;">
                <table class="w-full text-sm text-left">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="py-2 pr-4 font-semibold text-gray-600 dark:text-gray-400">Cámara</th>
                            <th class="py-2 pr-4 font-semibold text-gray-600 dark:text-gray-400">Servidor</th>
                            <th class="py-2 pr-4 font-semibold text-gray-600 dark:text-gray-400 text-center">Escribe a disco</th>
                            <th class="py-2 pr-4 font-semibold text-gray-600 dark:text-gray-400 text-center">FPS grabación</th>
                            <th class="py-2 font-semibold text-gray-600 dark:text-gray-400 text-center">Horas retenidas</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($anomalas as $camara)
                            <tr class="border-b border-gray-100 dark:border-gray-800">
                                <td class="py-2 pr-4 font-medium text-gray-900 dark:text-white">{{ $camara['nombre'] }}</td>
                                <td class="py-2 pr-4 text-gray-700 dark:text-gray-300">{{ $camara['servidor'] }}</td>
                                <td class="py-2 pr-4 text-center">
                                    <x-filament::badge :color="$camara['grabando'] ? 'success' : 'danger'">
                                        {{ $camara['grabando'] ? 'Sí' : 'No' }}
                                    </x-filament::badge>
                                </td>
                                <td class="py-2 pr-4 text-center {{ $camara['fps'] <= 0 ? 'text-danger-600 dark:text-danger-400 font-semibold' : 'text-gray-700 dark:text-gray-300' }}">
                                    {{ $camara['fps'] }}
                                </td>
                                <td class="py-2 text-center text-gray-700 dark:text-gray-300">
                                    {{ $camara['retencion'] !== null ? round($camara['retencion']) : '—' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
