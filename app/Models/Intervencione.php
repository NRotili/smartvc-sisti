<?php

namespace App\Models;

use App\Observers\NotificacioneObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

#[ObservedBy(NotificacioneObserver::class)]
class Intervencione extends Model
{
    use SoftDeletes, LogsActivity;

     public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll();
    }

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

    public function canBeEditedBy(User $user): bool
    {
        return (
                $this->created_at->equalTo($this->updated_at)
                && $this->user_id === $user->id
            )
            || $user->hasRole('Supervisor de Monitoreo');
    }
}
