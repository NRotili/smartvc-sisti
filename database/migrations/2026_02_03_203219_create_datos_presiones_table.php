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
        Schema::create('datos_presiones', function (Blueprint $table) {
            $table->id();
            //topic_id varchar indice
            $table->string('topic_id')->index();
            $table->foreign('topic_id')->references('topic_id')->on('sensores_presiones')->onDelete('cascade');
            //json datos null
            $table->json('message')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('datos_presiones');
    }
};
