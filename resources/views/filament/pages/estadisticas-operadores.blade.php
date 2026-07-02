<x-filament-panels::page>

    {{-- Filtro unificado --}}
    <x-filament::section heading="Filtros">
        <div style="display:flex; flex-wrap:wrap; gap:1.5rem; align-items:flex-end;">

            <div style="display:flex; flex-direction:column; gap:0.375rem;">
                <label class="fi-fo-field-wrp-label inline-flex items-center gap-x-3">
                    <span class="text-sm font-medium leading-6 text-gray-950 dark:text-white">Desde</span>
                </label>
                <x-filament::input.wrapper>
                    <x-filament::input type="date" wire:model="fechaInicio" />
                </x-filament::input.wrapper>
            </div>

            <div style="display:flex; flex-direction:column; gap:0.375rem;">
                <label class="fi-fo-field-wrp-label inline-flex items-center gap-x-3">
                    <span class="text-sm font-medium leading-6 text-gray-950 dark:text-white">Hasta</span>
                </label>
                <x-filament::input.wrapper>
                    <x-filament::input type="date" wire:model="fechaFin" />
                </x-filament::input.wrapper>
            </div>

            <div style="display:flex; flex-direction:column; gap:0.375rem; min-width:16rem;">
                <label class="fi-fo-field-wrp-label inline-flex items-center gap-x-3">
                    <span class="text-sm font-medium leading-6 text-gray-950 dark:text-white">Operador</span>
                </label>
                <x-filament::input.wrapper>
                    <select
                        wire:model="operadorId"
                        class="fi-select-input block w-full border-0 bg-transparent py-1.5 pe-8 text-gray-950 outline-none focus:ring-0 dark:text-white sm:text-sm"
                    >
                        <option value="">Todos los operadores</option>
                        @foreach($operadores as $op)
                            <option value="{{ $op->id }}">{{ $op->name }}</option>
                        @endforeach
                    </select>
                </x-filament::input.wrapper>
            </div>

            <div style="padding-bottom:1px;">
                <x-filament::button wire:click="actualizar" color="primary" icon="heroicon-m-magnifying-glass" size="md">
                    Aplicar filtros
                </x-filament::button>
            </div>

        </div>
    </x-filament::section>

    {{-- Tabla resumen --}}
    <x-filament::section :heading="$operadorNombre ? 'Resumen — ' . $operadorNombre : 'Resumen por operador'">
        @if(count($tableData) > 0)
            <table class="w-full text-sm text-left">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-700">
                        <th class="py-3 pr-6 font-semibold text-gray-600 dark:text-gray-400">Operador</th>
                        <th class="py-3 pr-6 font-semibold text-amber-600 dark:text-amber-400 text-center">Creó</th>
                        <th class="py-3 pr-6 font-semibold text-blue-600 dark:text-blue-400 text-center">Estuvo presente</th>
                        <th class="py-3 font-semibold text-gray-600 dark:text-gray-400 text-center">Total único</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tableData as $row)
                        <tr class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/50">
                            <td class="py-2 pr-6 font-medium text-gray-900 dark:text-white">{{ $row['nombre'] }}</td>
                            <td class="py-2 pr-6 text-center text-amber-600 dark:text-amber-400 font-semibold">{{ $row['creadas'] }}</td>
                            <td class="py-2 pr-6 text-center text-blue-600 dark:text-blue-400 font-semibold">{{ $row['participadas'] }}</td>
                            <td class="py-2 text-center font-bold text-gray-900 dark:text-white">{{ $row['total'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p class="text-center text-gray-500 dark:text-gray-400 py-4">No hay intervenciones en el período seleccionado.</p>
        @endif
    </x-filament::section>

    {{-- Los gráficos se renderizan como footerWidgets en la grilla nativa de Filament --}}

</x-filament-panels::page>
