<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ConocimientoIntervencione extends Pivot
{
    
    public function conocimiento()
    {
        return $this->belongsTo(Conocimiento::class);
    }
    
    public function intervencione()
    {
        return $this->belongsTo(Intervencione::class);
    }
}
