<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Camara extends Model
{
    use SoftDeletes;
        protected $fillable = [
        'nombre',
        'descripcion',
        'tipo_id',
        'lat',
        'lng',
        'cantIntervenciones',
        'server_id',
        'status',
        'publicada',
        'grabando',
        'mantenimiento',
        'activa',
        'ip',
        'url_imagen'
    ];

    public function tipo()
    {
        return $this->belongsTo(TipoCamaras::class);
    }

    public function server()
    {
        return $this->belongsTo(Servidores::class);
    }

    public function desperfectos()
    {
        return $this->hasMany(DesperfectosCamara::class, 'camara_id');
    }
}
