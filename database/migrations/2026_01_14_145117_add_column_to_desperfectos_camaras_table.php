<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('desperfectos_camaras', function (Blueprint $table) {
            $table->unsignedBigInteger('falla_camara_id')->nullable()->after('camara_id');
            $table->foreign('falla_camara_id')->references('id')->on('fallas_camaras')->onDelete('set null');
            $table->dropColumn('descripcion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('desperfectos_camaras', function (Blueprint $table) {
            $table->dropForeign('desperfectos_camaras_falla_camara_id_foreign');
            $table->dropColumn('falla_camara_id');
            $table->text('descripcion')->nullable()->after('hora_desperfecto');
        });
    }
};
