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
        Schema::create('roadmap_stages', function (Blueprint $table) {
        $table->id();
        $table->text('stage_description');
        $table->integer('stage_order');
        $table->text('requirements')->nullable();
        $table->foreignId('roadmap_id')
          ->constrained('roadmaps')
          ->cascadeOnDelete();
          
        $table->timestamps();   
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roadmap_stages');
    }
};