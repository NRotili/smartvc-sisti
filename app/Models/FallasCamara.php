<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FallasCamara extends Model
{
    protected $fillable = [
        'tipo_falla',
        'descripcion',
    ];
}
