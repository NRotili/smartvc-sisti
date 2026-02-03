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
        Schema::create('desperfectos_camaras', function (Blueprint $table) {
            $table->id();
            $table->date('fecha_desperfecto');
            $table->time('hora_desperfecto');
            $table->text('descripcion')->nullable();
            $table->unsignedBigInteger('camara_id');
            $table->foreign('camara_id')->references('id')->on('camaras')->onDelete('cascade');
            $table->date('fecha_solucion')->nullable();
            $table->time('hora_solucion')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('desperfectos_camaras');
    }
};
