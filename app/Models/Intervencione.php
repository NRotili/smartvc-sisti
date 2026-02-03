<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Intervencione extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'categoria_id',
        'user_id',
        'descripcion',
        'fecha_hora',
        'estado',
    ];

      //categoria
    public function categoria()
    {
        return $this->belongsTo(CategoriasIntervencione::class);
    }

    //usuario
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    //cámaras
    public function camaras()
    {
        return $this->hasMany(CamaraIntervencione::class);
    }

    //conocimientos
    public function conocimientos()
    {
        return $this->hasMany(ConocimientoIntervencione::class);
    }
}
