<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CategoriasIntervencione extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nombre',
    ];

    //intervenciones
    public function intervenciones()
    {
        return $this->hasMany(Intervencione::class);
    }
}
