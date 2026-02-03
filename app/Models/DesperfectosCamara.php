<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DesperfectosCamara extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'fecha_desperfecto',
        'hora_desperfecto',
        'camara_id',
        'falla_camara_id',
        'fecha_solucion',
        'hora_solucion',
        'observaciones',
    ];

    public function fallaCamara()
    {
        return $this->belongsTo(FallasCamara::class, 'falla_camara_id');
    }

    public function camara()
    {
        return $this->belongsTo(Camara::class, 'camara_id');
    }
}
