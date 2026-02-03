<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SensoresPresione extends Model
{
    protected $table = 'sensores_presiones';

    use SoftDeletes;

    protected $fillable = [
        'nombre',
        'topic_id',
        'presion_minima',
        'presion_maxima',
        'observaciones',
    ];

    public function datosPresiones()
    {
        return $this->hasMany(DatosPresione::class, 'topic_id', 'topic_id');
    }
}
