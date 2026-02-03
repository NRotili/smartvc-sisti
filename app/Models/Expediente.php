<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expediente extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'fecha_ingreso',
        'numero_expediente',
        'iniciador_expediente_id',
        'numero_nota',
        'fecha_hora_inicio_exportacion',
        'fecha_hora_fin_exportacion',
        'material_adjunto',
        'fecha_entrega',
        'observaciones',
    ];

    public function iniciadorExpediente()
    {
        return $this->belongsTo(IniciadoresExpediente::class, 'iniciador_expediente_id');
    }

    public function camaras()
    {
        return $this->belongsToMany(Camara::class, 'camara_expediente', 'expediente_id', 'camara_id')
                    ->withTimestamps();
    }
}
