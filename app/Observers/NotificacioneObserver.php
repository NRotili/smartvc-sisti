<?php

namespace App\Observers;

use App\Models\Intervencione;
use Filament\Notifications\Notification;
use Illuminate\Container\Attributes\Auth;
use Telegram\Bot\Laravel\Facades\Telegram;

class NotificacioneObserver
{
    /**
     * Handle the Intervencione "created" event.
     */
    public function created(Intervencione $intervencione): void
    {
        $creator = $intervencione->user;
        Notification::make()
            ->title('Nueva intervención creada')
            ->body("Se creó la intervención #{$intervencione->id}.")
            ->success()
            ->sendToDatabase($creator);

        //Enviar mensaje por telegram incluyendo fecha y hora, categoría, descripción y usuario a canal_monitoreo_intervenciones
        Telegram::sendMessage([
            'chat_id' => config('services.telegram.canal_monitoreo_intervenciones'),
            'text' => "📢 *Nueva Intervención Creada* 📢\n\n*ID:* #{$intervencione->id}\n*Fecha y Hora:* {$intervencione->fecha_hora}\n*Categoría:* {$intervencione->categoria->nombre}\n*Descripción:* {$intervencione->descripcion}\n*Usuario:* {$creator->name}",
            'parse_mode' => 'Markdown'
        ]);
    }

    /**
     * Handle the Intervencione "updated" event.
     */
    public function updated(Intervencione $intervencione): void
    {
        $editor = auth()->user();
        
        if ($intervencione->user != $editor){
            Notification::make()
                ->title('Intervención actualizada')
                ->body("Tu intervención #{$intervencione->id} fue editada por {$editor->name}.")
                ->warning()
                ->sendToDatabase($intervencione->user);
        } else {
            Notification::make()
                ->title('Intervención actualizada')
                ->body("Tu intervención #{$intervencione->id} fue editada.")
                ->warning()
                ->sendToDatabase($intervencione->user);
        }
    }

    /**
     * Handle the Intervencione "deleted" event.
     */
    public function deleted(Intervencione $intervencione): void
    {
        $eliminador = auth()->user();
        Notification::make()
            ->title('Intervención eliminada')
            ->body("Tu intervención #{$intervencione->id} fue eliminada por {$eliminador->name}.")
            ->danger()
            ->sendToDatabase($intervencione->user);
    }

    /**
     * Handle the Intervencione "restored" event.
     */
    public function restored(Intervencione $intervencione): void
    {
        $restaurador = auth()->user();
        Notification::make()
            ->title('Intervención restaurada')
            ->body("Tu intervención #{$intervencione->id} fue restaurada por {$restaurador->name}.")
            ->success()
            ->sendToDatabase($intervencione->user);
    }

    /**
     * Handle the Intervencione "force deleted" event.
     */
    public function forceDeleted(Intervencione $intervencione): void
    {
        //
    }
}
