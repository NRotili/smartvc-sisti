<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuscripcionReporte extends Model
{
    protected $table = 'suscripciones_reportes';

    protected $fillable = [
        'user_id',
        'reporte',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Emails de los usuarios suscriptos a un reporte. Si no hay ninguno,
     * cae a los destinatarios de config/reportes.php para que el reporte
     * nunca deje de salir por una config vacía.
     *
     * @return array<int, string>
     */
    public static function destinatarios(string $reporte, array $fallback = []): array
    {
        $emails = static::where('reporte', $reporte)
            ->with('user')
            ->get()
            ->pluck('user.email')
            ->filter()
            ->values()
            ->all();

        return $emails !== [] ? $emails : $fallback;
    }
}
