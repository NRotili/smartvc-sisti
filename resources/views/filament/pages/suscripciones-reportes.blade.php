<x-filament-panels::page>

    <x-filament::section heading="Destinatarios por reporte" description="Cada reporte se envía por email a los usuarios seleccionados. Si un reporte queda sin usuarios, se usan los destinatarios definidos en config/reportes.php.">
        <form wire:submit.prevent="guardar">
            {{ $this->form }}

            <div style="margin-top:1.5rem;">
                <x-filament::button type="submit" color="primary" icon="heroicon-m-check" size="md">
                    Guardar
                </x-filament::button>
            </div>
        </form>
    </x-filament::section>

</x-filament-panels::page>
