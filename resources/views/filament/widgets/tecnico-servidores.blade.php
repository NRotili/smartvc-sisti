<x-filament-widgets::widget>
    <x-filament::section heading="Servidores Digifort" icon="heroicon-o-server-stack">
        @if($servidores->isEmpty())
            <p class="text-center text-gray-500 dark:text-gray-400 py-4">No hay servidores de monitoreo cargados.</p>
        @else
            <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(16rem, 1fr)); gap:1rem;">
                @foreach($servidores as $servidor)
                    <div class="rounded-xl border border-gray-200 dark:border-gray-700 p-4" style="display:flex; flex-direction:column; gap:0.5rem;">
                        <div style="display:flex; justify-content:space-between; align-items:center;">
                            <span class="font-semibold text-gray-900 dark:text-white">{{ $servidor['nombre'] }}</span>
                            @if($servidor['online'])
                                <x-filament::badge color="success">En línea</x-filament::badge>
                            @else
                                <x-filament::badge color="danger">Sin respuesta</x-filament::badge>
                            @endif
                        </div>

                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ $servidor['ip'] }}@if($servidor['version']) — Digifort {{ $servidor['version'] }}@endif</span>

                        @if($servidor['online'])
                            <div class="text-sm text-gray-700 dark:text-gray-300">
                                <span class="font-medium">Tiempo activo:</span> {{ $servidor['uptime'] }}
                            </div>

                            @if($servidor['cpu'] !== null)
                                <div class="text-sm text-gray-700 dark:text-gray-300">
                                    <span class="font-medium">CPU:</span>
                                    <span @class(['text-danger-600 dark:text-danger-400 font-semibold' => $servidor['cpu'] >= 85])>{{ $servidor['cpu'] }}%</span>
                                    <span class="font-medium">· RAM:</span> {{ $servidor['ram'] }} GB
                                </div>
                                <div class="text-sm text-gray-700 dark:text-gray-300">
                                    <span class="font-medium">Tráfico:</span>
                                    ↓ {{ $servidor['trafico_entrada'] }} Mbps · ↑ {{ $servidor['trafico_salida'] }} Mbps
                                </div>
                            @endif

                            @if($servidor['disco_tb'] !== null)
                                <div class="text-sm text-gray-700 dark:text-gray-300">
                                    <span class="font-medium">Grabaciones en disco:</span> {{ $servidor['disco_tb'] }} TB
                                </div>
                            @endif

                            @if($servidor['licencias'])
                                <div class="text-sm text-gray-700 dark:text-gray-300">
                                    <span class="font-medium">Licencias:</span>
                                    {{ $servidor['licencias']['usadas'] }} / {{ $servidor['licencias']['total'] }} en uso
                                </div>
                            @endif

                            @if($servidor['usuarios'] !== null)
                                <div class="text-sm text-gray-700 dark:text-gray-300">
                                    <span class="font-medium">Usuarios conectados:</span>
                                    @if($servidor['usuarios']->isEmpty())
                                        ninguno
                                    @else
                                        {{ $servidor['usuarios']->implode(', ') }}
                                    @endif
                                </div>
                            @endif

                            @if($servidor['camaras'])
                                <div style="display:flex; gap:0.5rem; flex-wrap:wrap; margin-top:0.25rem;">
                                    <x-filament::badge color="gray">{{ $servidor['camaras']['total'] }} cámaras</x-filament::badge>
                                    <x-filament::badge :color="$servidor['camaras']['caidas'] > 0 ? 'danger' : 'success'">
                                        {{ $servidor['camaras']['caidas'] }} caídas
                                    </x-filament::badge>
                                    <x-filament::badge :color="$servidor['camaras']['sin_grabar'] > 0 ? 'warning' : 'success'">
                                        {{ $servidor['camaras']['sin_grabar'] }} sin grabar
                                    </x-filament::badge>
                                </div>
                            @endif
                        @else
                            <p class="text-sm text-danger-600 dark:text-danger-400">No se pudo conectar al servidor. Verificar red / servicio Digifort.</p>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
