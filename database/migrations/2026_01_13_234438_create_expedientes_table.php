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
        Schema::create('expedientes', function (Blueprint $table) {
            $table->id();
            $table->date('fecha_ingreso');
            $table->string('numero_expediente');
            $table->unsignedBigInteger('iniciador_expediente_id')->nullable();
            $table->foreign('iniciador_expediente_id')->references('id')->on('iniciadores_expedientes')->onDelete('set null');
            $table->string('numero_nota')->nullable();
            $table->dateTime('fecha_hora_inicio_exportacion');
            $table->dateTime('fecha_hora_fin_exportacion');
            $table->string('material_adjunto')->nullable();
            $table->date('fecha_entrega')->nullable();
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
        Schema::dropIfExists('expedientes');
    }
};
