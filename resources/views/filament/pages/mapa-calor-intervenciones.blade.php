<x-filament-panels::page>

    {{-- Filtros --}}
    <x-filament::section heading="Filtros">
        <form wire:submit.prevent="actualizar">
            <div style="display:flex; flex-wrap:wrap; gap:1.5rem; align-items:flex-end;">
                <div style="flex:1; min-width:20rem;">
                    {{ $this->form }}
                </div>
                <div style="padding-bottom:1px;">
                    <x-filament::button type="submit" color="primary" icon="heroicon-m-magnifying-glass" size="md">
                        Aplicar filtros
                    </x-filament::button>
                </div>
            </div>
        </form>
    </x-filament::section>

    {{-- Mapa --}}
    <x-filament::section heading="Zonas con más intervenciones">
        <div wire:ignore>
            <link rel="stylesheet" href="{{ asset('vendor/leaflet/leaflet.css') }}">
            <style>
                /* El pane del calor debe quedar sobre las tiles (z-index 200); se define
                   por CSS con !important porque el estilo inline se pierde */
                .leaflet-panelCalor-pane {
                    z-index: 650 !important;
                    pointer-events: none;
                }
            </style>
            <div id="mapa-calor-intervenciones" style="height:600px; border-radius:0.75rem; z-index:0;"></div>

            <script src="{{ asset('vendor/leaflet/leaflet.js') }}"></script>
            <script src="{{ asset('vendor/leaflet/leaflet-heat.js') }}?v=3"></script>
            <script>
                // Se captura window.L en el closure: el paquete filament-leaflet
                // carga su propio Leaflet (sin el plugin heat) y pisa el global después
                (function (L) {
                    const mapa = L.map('mapa-calor-intervenciones').setView([-33.233425, -60.324238], 14);

                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,
                        attribution: '&copy; OpenStreetMap contributors'
                    }).addTo(mapa);

                    // Pane dedicado con z-index inline: garantiza que el calor quede
                    // sobre las tiles aunque el CSS de los paneles de Leaflet sea
                    // pisado por otros estilos del panel
                    mapa.createPane('panelCalor');
                    mapa.getPane('panelCalor').style.zIndex = 650;
                    mapa.getPane('panelCalor').style.pointerEvents = 'none';

                    let capaCalor = null;
                    let capaMarcadores = L.layerGroup().addTo(mapa);

                    function dibujar(puntos) {
                        if (capaCalor) {
                            mapa.removeLayer(capaCalor);
                        }
                        capaMarcadores.clearLayers();

                        if (!puntos.length) {
                            return;
                        }

                        const max = Math.max(...puntos.map(p => p.total));

                        // Escala logarítmica: la mayoría de las cámaras tiene pocas
                        // intervenciones y en escala lineal quedan invisibles frente al máximo
                        capaCalor = L.heatLayer(
                            puntos.map(p => [p.lat, p.lng, Math.log(1 + p.total) / Math.log(1 + max)]),
                            // maxZoom = zoom inicial: leaflet.heat reduce la intensidad a la
                            // mitad por cada nivel de zoom por debajo de maxZoom
                            { radius: 35, blur: 25, max: 1.0, maxZoom: 14, minOpacity: 0.2, pane: 'panelCalor' }
                        ).addTo(mapa);

                        // Marcadores invisibles para tooltips con el detalle por cámara
                        puntos.forEach(p => {
                            L.circleMarker([p.lat, p.lng], {
                                radius: 12,
                                stroke: false,
                                fillOpacity: 0,
                            })
                            .bindTooltip(`<strong>${p.nombre}</strong><br>${p.total} intervencion${p.total === 1 ? '' : 'es'}`)
                            .addTo(capaMarcadores);
                        });
                    }

                    dibujar(@json($puntos));

                    document.addEventListener('livewire:init', () => {
                        Livewire.on('heatmap-actualizado', (event) => {
                            dibujar(event.puntos ?? []);
                        });
                    });
                })(window.L);
            </script>
        </div>
    </x-filament::section>

    {{-- Ranking --}}
    <x-filament::section heading="Cámaras con más intervenciones en el período">
        @if($topCamaras->isEmpty())
            <p class="text-center text-gray-500 dark:text-gray-400 py-4">No hay intervenciones con cámaras georreferenciadas en el período seleccionado.</p>
        @else
            <table class="w-full text-sm text-left">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-700">
                        <th class="py-3 pr-6 font-semibold text-gray-600 dark:text-gray-400">#</th>
                        <th class="py-3 pr-6 font-semibold text-gray-600 dark:text-gray-400">Cámara</th>
                        <th class="py-3 font-semibold text-gray-600 dark:text-gray-400 text-center">Intervenciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($topCamaras as $i => $fila)
                        <tr class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/50">
                            <td class="py-2 pr-6 text-gray-500 dark:text-gray-400">{{ $i + 1 }}</td>
                            <td class="py-2 pr-6 font-medium text-gray-900 dark:text-white">{{ $fila['nombre'] }}</td>
                            <td class="py-2 text-center font-bold text-gray-900 dark:text-white">{{ $fila['total'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </x-filament::section>

</x-filament-panels::page>
