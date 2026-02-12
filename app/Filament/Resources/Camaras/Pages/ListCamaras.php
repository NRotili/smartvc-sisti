<?php

namespace App\Filament\Resources\Camaras\Pages;

use App\Filament\Resources\Camaras\CamaraResource;
use App\Models\Camara;
use App\Models\Servidores;
use App\Models\TipoCamaras;
use Barryvdh\Debugbar\Facades\Debugbar;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ListCamaras extends ListRecords
{
    protected static string $resource = CamaraResource::class;
    protected static ?string $title = 'Cámaras';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Agregar Cámara'),
            Action::make('sincronizarCamaras')
                ->label('Sincronizar Cámaras')
                ->color('danger')
                ->requiresConfirmation()
                ->action(function () {
                    $serverMonitoreo = Servidores::where('descripcion', 'like', '%Monitoreo%')->get();
                    foreach ($serverMonitoreo as $server) {
                        Debugbar::info($server->ip);

                        try {
                            $responses = Http::timeout(10)->get("http://$server->ip:8601/Interface/Cameras/GetCameras?Fields=Name,%20Latitude,%20Longitude,%20ConnectionAddress,%20Description&ResponseFormat=JSON&AuthUser=" . env('DIGIFORT_USER') . "&AuthPass=" . env('DIGIFORT_PASSWORD'))->json();
                            foreach ($responses['Response']['Data']['Cameras'] as $response) {
                                Debugbar::info($response['Name']);

                                $camera = Camara::where('nombre', $response['Name'])->first();

                                $responses2 = Http::timeout(10)->get("http://$server->ip:8601/Interface/Cameras/GetStatus?Cameras=" . $response['Name'] . "&ResponseFormat=JSON&AuthUser=" . env('DIGIFORT_USER') . "&AuthPass=" . env('DIGIFORT_PASSWORD'))->json();
                                Debugbar::info($responses2);

                                //Si $response['Name'], 'Domo') contiene la palabra Domo, es de tipo Domo, si contiene Fija es de tipo Bullet, si contiene LPR es de tipo LPR. 
                                if (Str::contains($response['Name'], 'Domo')) {
                                    $type = TipoCamaras::where('tipo', 'Domo')->first()->id;
                                } elseif (Str::contains($response['Name'], 'Fija')) {
                                    $type = TipoCamaras::where('tipo', 'Bullet')->first()->id;
                                } elseif (Str::contains($response['Name'], 'LPR')) {
                                    $type = TipoCamaras::where('tipo', 'LPR')->first()->id;
                                }

                                Debugbar::info("Tipo de cámara: $type");


                                foreach ($responses2['Response']['Data']['Cameras'] as $response2) {
                                    if ($camera) {
                                        if ($camera->lat != $response['Latitude'] || $camera->lng != $response['Longitude']  || $camera->status != $response2['Working'] || $camera->ip != $response['ConnectionAddress'] || $camera->descripcion != $response['Description'] || $camera->activa != $response2['Active'] || $camera->grabando != $response2['WrittingToDisk']) {
                                            $url_imagen = "http://" . $server->ip . ":8601/Interface/Cameras/GetJPEGStream?Camera=" . $response['Name'] . "&AuthUser=" . env('DIGIFORT_USER') . "&AuthPass=" . env('DIGIFORT_PASSWORD');
                                            //encode Url
                                            $url_imagen = str_replace(" ", "%20", $url_imagen);
                                            $camera->update([
                                                'lat' => $response['Latitude'],
                                                'lng' => $response['Longitude'],
                                                'status' => $response2['Working'],
                                                'activa' => $response2['Active'],
                                                'grabando' => $response2['WrittingToDisk'],
                                                'server_id' => $server->id,
                                                'ip' => $response['ConnectionAddress'],
                                                'descripcion' => $response['Description'],
                                                'url_imagen' => $url_imagen,
                                            ]);
                                        }
                                    } else {
                                        $url_imagen = "http://" . $server->ip . ":8601/Interface/Cameras/GetJPEGStream?Camera=" . $response['Name'] . "&AuthUser=" . env('DIGIFORT_USER') . "&AuthPass=" . env('DIGIFORT_PASSWORD');
                                        //encode Url
                                        $url_imagen = str_replace(" ", "%20", $url_imagen);
                                        Camara::create([
                                            'nombre' => $response['Name'],
                                            'lat' => $response['Latitude'],
                                            'lng' => $response['Longitude'],
                                            'status' => $response2['Working'],
                                            'activa' => $response2['Active'],
                                            'grabando' => $response2['WrittingToDisk'],
                                            'cantIntervenciones' => 0,
                                            'server_id' => $server->id,
                                            'tipo_id' => $type,
                                            'ip' => $response['ConnectionAddress'],
                                            'descripcion' => $response['Description'],
                                            'url_imagen' => $url_imagen,
                                        ]);
                                    }
                                }
                            }
                        } catch (\Throwable $th) {
                            //Error 500
                            Debugbar::error($th);
                        }
                    }
                })
                ->authorize(
                    fn() =>
                    auth()->user()->hasAnyRole([
                        'super_admin',
                        'Supervisor de Monitoreo',
                        'Técnico de Monitoreo',
                    ])
                )
        ];
    }
}
