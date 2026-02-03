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
        Schema::create('camara_intervencione', function (Blueprint $table) {
            $table->id();
            $table->foreignId('camara_id')->constrained()->cascadeOnDelete();
            $table->foreignId('intervencione_id')->constrained()->cascadeOnDelete();
            $table->timestamp('fecha_hora');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('camara_intervencione');
    }
};
