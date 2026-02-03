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
        Schema::create('conocimiento_intervencione', function (Blueprint $table) {
            $table->id();

            $table->foreignId('conocimiento_id')->constrained();
            $table->foreignId('intervencione_id')->constrained();
            $table->string('manifiesto', 1000);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conocimiento_intervencione');
    }
};
