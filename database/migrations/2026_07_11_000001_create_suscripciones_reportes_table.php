<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suscripciones_reportes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('reporte');
            $table->timestamps();
            $table->unique(['user_id', 'reporte']);
        });

        // Migrar los destinatarios actuales de config/reportes.php
        $destinatarios = [
            'diario' => config('reportes.destinatarios', []),
            'mensual_operadores' => config('reportes.destinatarios_operadores', []),
        ];

        foreach ($destinatarios as $reporte => $emails) {
            User::whereIn('email', $emails)->pluck('id')->each(
                fn ($userId) => DB::table('suscripciones_reportes')->insert([
                    'user_id' => $userId,
                    'reporte' => $reporte,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('suscripciones_reportes');
    }
};
