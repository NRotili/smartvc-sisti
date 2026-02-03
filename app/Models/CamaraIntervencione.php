<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Relations\Pivot;

class CamaraIntervencione extends Pivot
{
    protected $casts = [
        'fecha_hora' => 'datetime',
    ];

    public function camara()
    {
        return $this->belongsTo(Camara::class);
    }

    public function intervencione()
    {
        return $this->belongsTo(Intervencione::class);
    }
}
