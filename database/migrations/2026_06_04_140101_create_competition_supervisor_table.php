<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('competition_supervisor', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competition_id')
                ->constrained()
                ->onDelete('cascade');
            $table->foreignId('supervisor_id')
                ->constrained('users')
                ->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('competition_supervisor');
    }
};