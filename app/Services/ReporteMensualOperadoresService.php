<?php

namespace App\Services;

use App\Models\Intervencione;
use App\Models\User;
use Carbon\Carbon;

class ReporteMensualOperadoresService
{
    /**
     * Estadísticas de operadores de un mes (por defecto, el mes anterior).
     *
     * @return array{mes: string, total_equipo: int, variacion: ?int, operadores: array, top_categorias: array}
     */
    public function generar(?Carbon $mes = null): array
    {
        $inicio = ($mes ?? now()->subMonthNoOverflow())->copy()->startOfMonth();
        $fin = $inicio->copy()->endOfMonth();

        $totalEquipo = Intervencione::whereBetween('fecha_hora', [$inicio, $fin])->count();

        $totalMesAnterior = Intervencione::whereBetween('fecha_hora', [
            $inicio->copy()->subMonthNoOverflow()->startOfMonth(),
            $inicio->copy()->subMonthNoOverflow()->endOfMonth(),
        ])->count();

        $operadores = User::role('Operador de Monitoreo')
            ->orderBy('name')
            ->get()
            ->map(fn (User $user) => $this->statsOperador($user, $inicio, $fin, $totalEquipo))
            ->sortByDesc('total')
            ->values()
            ->all();

        $topCategorias = Intervencione::selectRaw('categoria_id, COUNT(*) as total')
            ->whereBetween('fecha_hora', [$inicio, $fin])
            ->groupBy('categoria_id')
            ->orderByDesc('total')
            ->limit(5)
            ->with('categoria')
            ->get()
            ->map(fn ($fila) => [
                'nombre' => $fila->categoria?->nombre ?? 'Sin categoría',
                'total' => (int) $fila->total,
            ])
            ->all();

        return [
            'mes' => ucfirst($inicio->locale('es')->translatedFormat('F Y')),
            'total_equipo' => $totalEquipo,
            'variacion' => $totalMesAnterior > 0
                ? (int) round((($totalEquipo - $totalMesAnterior) / $totalMesAnterior) * 100)
                : null,
            'operadores' => $operadores,
            'top_categorias' => $topCategorias,
        ];
    }

    private function statsOperador(User $user, Carbon $inicio, Carbon $fin, int $totalEquipo): array
    {
        $creadas = Intervencione::where('user_id', $user->id)
            ->whereBetween('fecha_hora', [$inicio, $fin])
            ->get();

        $creadasIds = $creadas->pluck('id');

        $presentes = $user->intervencionesMonitoreo()
            ->whereBetween('fecha_hora', [$inicio, $fin])
            ->whereNotIn('intervenciones.id', $creadasIds)
            ->count();

        $total = $creadas->count() + $presentes;

        // Carga puntual: creadas dentro de las 2 horas del hecho (mismo umbral
        // que el accessor fuera_de_plazo de Intervencione)
        $enPlazo = $creadas->filter(function (Intervencione $intervencion) {
            $horas = Carbon::parse($intervencion->fecha_hora)->diffInHours(Carbon::parse($intervencion->created_at));

            return $horas < 2;
        })->count();

        return [
            'nombre' => $user->name,
            'creadas' => $creadas->count(),
            'presentes' => $presentes,
            'total' => $total,
            'participacion' => $totalEquipo > 0 ? (int) round(($total / $totalEquipo) * 100) : 0,
            'en_plazo' => $creadas->count() > 0 ? (int) round(($enPlazo / $creadas->count()) * 100) : null,
        ];
    }
}
