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
        Schema::create('camaras', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('descripcion');
            $table->foreignId('tipo_id')->nullable()->constrained('tipo_camaras');
            $table->string('lat')->nullable();
            $table->string('lng')->nullable();
            $table->bigInteger('cantIntervenciones')->nullable();
            $table->foreignId('server_id')->nullable()->constrained('servidores');
            $table->boolean('status');
            $table->boolean('publicada')->default(1);
            $table->boolean('grabando')->default(1);
            $table->boolean('mantenimiento')->default(0); 
            $table->boolean('activa')->default(1);
            $table->string('ip');
            $table->string('url_imagen');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('camaras');
    }
};
