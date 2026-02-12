<?php

use App\Models\SensoresPresione;
use App\Notifications\AlertaNotification;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Route;
use Telegram\Bot\Laravel\Facades\Telegram;

use function Symfony\Component\Translation\t;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/get-updates', function () {

    return Telegram::getUpdates();
});

Route::get('/send-message', function () {
    Telegram::sendMessage([
        'chat_id' => 'reemplazar con id de chat de service.php ',
        'text' => 'Hello World'
    ]);
});



Route::get('telegram-agua-presion', function () {

    $sensoresPresion = SensoresPresione::all();
    foreach ($sensoresPresion as $sensor) {

        $data = $sensor->datosPresiones()->latest()->first();
        $presion = json_decode($data->message, true);
        $valorDePresion = round($presion['values']['Presion'] / 10, 2);

        if ($presion && ($valorDePresion <= $sensor->presion_minima || $valorDePresion >= $sensor->presion_maxima)) {

            Telegram::sendMessage([
                'chat_id' => config('services.telegram.canal_agua_presion'),
                'text' => "⚠ *ALERTA DE PRESIÓN* ⚠ \n\n*Sensor:* $sensor->nombre \n*Presión:* " . $valorDePresion,
                'parse_mode' => 'Markdown'
            ]);
        }
    }
});