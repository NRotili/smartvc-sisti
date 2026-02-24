<?php

namespace App\Observers;

use App\Models\DesperfectosCamara;
use Filament\Notifications\Notification;
use Telegram\Bot\Laravel\Facades\Telegram;

class DesperfectosCamaraObserver
{
    /**
     * Handle the DesperfectosCamara "created" event.
     */
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
        try {
            Telegram::sendMessage([
                'chat_id' => config('services.telegram.canal_monitoreo_fallas'),
                'text' => "⚠️ *Desperfecto de Cámara Actualizado* ⚠️\n\n*ID:* #{$desperfectosCamara->id}\n*Fecha:* {$desperfectosCamara->fecha_desperfecto} {$desperfectosCamara->hora_desperfecto}\n*Cámara:* {$desperfectosCamara->camara->nombre} - {$desperfectosCamara->camara->descripcion}\n*Estado:* " . ($desperfectosCamara->fecha_solucion ? "Solucionado el {$desperfectosCamara->fecha_solucion} a las {$desperfectosCamara->hora_solucion}" : "Pendiente de solución"),
                'parse_mode' => 'Markdown'
            ]);
        } catch (\Throwable $th) {
            //Enviar notificación a los que tienen rol super_admin
            Notification::make()
                ->title('Error al enviar notificación de Telegram')
                ->body("Ocurrió un error al enviar la notificación de Telegram para el Desperfecto de Cámara #{$desperfectosCamara->id}. Error: {$th->getMessage()}")
                ->danger()
                ->sendToDatabaseRole('super_admin');
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
