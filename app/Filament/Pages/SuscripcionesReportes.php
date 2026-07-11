<?php

namespace App\Filament\Pages;

use App\Models\SuscripcionReporte;
use App\Models\User;
use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Artisan;
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

    protected function getHeaderActions(): array
    {
        return [
            Action::make('ejecutar')
                ->label('Ejecutar reporte')
                ->icon('heroicon-m-paper-airplane')
                ->modalHeading('Ejecutar un reporte puntual')
                ->modalDescription('El reporte se genera y se envía por email a los suscriptos actuales.')
                ->modalSubmitActionLabel('Enviar')
                ->form([
                    Select::make('reporte')
                        ->label('Reporte')
                        ->options(config('reportes.tipos'))
                        ->required()
                        ->live()
                        ->native(false),
                    DatePicker::make('fecha')
                        ->label('Día a reportar')
                        ->native(false)
                        ->displayFormat('d/m/Y')
                        ->default(now()->subDay())
                        ->maxDate(now())
                        ->required()
                        ->visible(fn (Get $get): bool => $get('reporte') === 'diario'),
                    Select::make('mes')
                        ->label('Mes a reportar')
                        ->options(
                            collect(range(1, 12))->mapWithKeys(function (int $i) {
                                $mes = now()->subMonthsNoOverflow($i);

                                return [$mes->format('Y-m') => ucfirst($mes->locale('es')->translatedFormat('F Y'))];
                            })
                        )
                        ->default(now()->subMonthNoOverflow()->format('Y-m'))
                        ->required()
                        ->native(false)
                        ->visible(fn (Get $get): bool => $get('reporte') === 'mensual_operadores'),
                ])
                ->action(function (array $data) {
                    if ($data['reporte'] === 'diario') {
                        Artisan::call('reporte:diario', ['--fecha' => $data['fecha']]);
                        $destinatarios = SuscripcionReporte::destinatarios('diario', config('reportes.destinatarios'));
                    } else {
                        Artisan::call('reporte:mensual-operadores', ['--mes' => $data['mes']]);
                        $destinatarios = SuscripcionReporte::destinatarios('mensual_operadores', config('reportes.destinatarios_operadores'));
                    }

                    Notification::make()
                        ->title('Reporte encolado para envío')
                        ->body('Se enviará a '.count($destinatarios).' destinatario'.(count($destinatarios) === 1 ? '' : 's').'.')
                        ->success()
                        ->send();
                }),
        ];
    }

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
