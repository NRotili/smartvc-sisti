<?php

namespace App\Observers;

use App\Models\DesperfectosCamara;
use App\Models\User;
use Barryvdh\Debugbar\Facades\Debugbar;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Telegram\Bot\Laravel\Facades\Telegram;

class DesperfectosCamaraObserver
{

    public function created(DesperfectosCamara $desperfectosCamara): void
    {
        Telegram::sendMessage([
            'chat_id' => config('services.telegram.canal_monitoreo_fallas'),
            'text' => "⚠️ *Nuevo Desperfecto de Cámara* ⚠️\n\n*ID:* #{$desperfectosCamara->id}\n*Fecha:* {$desperfectosCamara->fecha_desperfecto} {$desperfectosCamara->hora_desperfecto}\n*Cámara:* {$desperfectosCamara->camara->nombre} - {$desperfectosCamara->camara->descripcion}",
            'parse_mode' => 'Markdown'
        ]);
    }

    /**
     * Handle the DesperfectosCamara "updated" event.
     */
    public function updated(DesperfectosCamara $desperfectosCamara): void
    {

        //calcular tiempo transcurrido entre fecha_desperfecto y hora_desperfecto con fecha_solucion y hora_solucion
        $tiempo_transcurrido = null;
        if ($desperfectosCamara->fecha_solucion && $desperfectosCamara->hora_solucion) {
            $fecha_desperfecto = Carbon::createFromFormat(
                'Y-m-d H:i:s',
                "{$desperfectosCamara->fecha_desperfecto} {$desperfectosCamara->hora_desperfecto}"
            );

            $fecha_solucion = Carbon::createFromFormat(
                'Y-m-d H:i:s',
                "{$desperfectosCamara->fecha_solucion} {$desperfectosCamara->hora_solucion}"
            );

            $tiempo_transcurrido = $fecha_desperfecto->diffForHumans($fecha_solucion, true);
        }


        try {
            Telegram::sendMessage([
                'chat_id' => config('services.telegram.canal_monitoreo_fallas'),
                'text' => "⚠️ *Desperfecto de Cámara Actualizado* ⚠️\n\n*ID:* #{$desperfectosCamara->id}\n*Fecha:* {$desperfectosCamara->fecha_desperfecto} {$desperfectosCamara->hora_desperfecto}\n*Cámara:* {$desperfectosCamara->camara->nombre} - {$desperfectosCamara->camara->descripcion}\n*Tiempo transcurrido:* {$tiempo_transcurrido}\n*Estado:* " . ($desperfectosCamara->fecha_solucion ? "Solucionado el {$desperfectosCamara->fecha_solucion} a las {$desperfectosCamara->hora_solucion}" : "Pendiente de solución"),
                'parse_mode' => 'Markdown'
            ]);

        } catch (\Throwable $th) {
            //Enviar notificación a los que tienen rol super_admin
            User::role('super_admin')
                ->get()
                ->each(function ($user) use ($th) {
                    Notification::make()
                        ->title('Error al enviar notificación de Telegram')
                        ->body($th->getMessage())
                        ->danger()
                        ->sendToDatabase($user);
                });
        }
    }

    /**
     * Handle the DesperfectosCamara "deleted" event.
     */
    public function deleted(DesperfectosCamara $desperfectosCamara): void
    {
        //
    }

    /**
     * Handle the DesperfectosCamara "restored" event.
     */
    public function restored(DesperfectosCamara $desperfectosCamara): void
    {
        //
    }

    /**
     * Handle the DesperfectosCamara "force deleted" event.
     */
    public function forceDeleted(DesperfectosCamara $desperfectosCamara): void
    {
        //
    }
}
