<?php

namespace App\Filament\Pages;

use App\Models\SuscripcionReporte;
use App\Models\User;
use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class SuscripcionesReportes extends Page
{
    use HasPageShield;

    protected static ?string $title = 'Suscripciones de Reportes';

    protected static UnitEnum|string|null $navigationGroup = 'Sistema';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Envelope;

    protected string $view = 'filament.pages.suscripciones-reportes';

    /** @var array<string, mixed> */
    public ?array $data = [];

    public function mount(): void
    {
        $suscripciones = SuscripcionReporte::all()->groupBy('reporte');

        $this->form->fill(
            collect(config('reportes.tipos'))
                ->mapWithKeys(fn (string $label, string $tipo) => [
                    $tipo => $suscripciones->get($tipo)?->pluck('user_id')->all() ?? [],
                ])
                ->all()
        );
    }

    public function form(Schema $schema): Schema
    {
        $usuarios = User::orderBy('name')
            ->get()
            ->mapWithKeys(fn (User $user) => [$user->id => "{$user->name} ({$user->email})"]);

        return $schema
            ->components(
                collect(config('reportes.tipos'))
                    ->map(fn (string $label, string $tipo) => Select::make($tipo)
                        ->label($label)
                        ->multiple()
                        ->options($usuarios)
                        ->searchable()
                        ->native(false)
                        ->placeholder('Sin suscriptos — se usan los destinatarios de config/reportes.php')
                    )
                    ->values()
                    ->all()
            )
            ->statePath('data');
    }

    public function guardar(): void
    {
        $data = $this->form->getState();

        foreach (array_keys(config('reportes.tipos')) as $tipo) {
            $userIds = $data[$tipo] ?? [];

            SuscripcionReporte::where('reporte', $tipo)
                ->whereNotIn('user_id', $userIds)
                ->delete();

            foreach ($userIds as $userId) {
                SuscripcionReporte::firstOrCreate([
                    'user_id' => $userId,
                    'reporte' => $tipo,
                ]);
            }
        }

        Notification::make()
            ->title('Suscripciones guardadas')
            ->success()
            ->send();
    }
}
