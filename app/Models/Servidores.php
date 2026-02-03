<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Servidores extends Model
{
    use SoftDeletes;
     protected $fillable = [
        'nombre',
        'descripcion',
        'ip',
        'estado',
    ];
}
