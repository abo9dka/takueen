<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /** * Run the migrations. */ public function up(): void
    {
        Schema::create('competitions', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['upcoming', 'ongoing', 'completed'])->default('upcoming');
            $table->text('link_register_competition')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('supervisor_id')->nullable()->after('user_id');
            $table->foreign('supervisor_id')->references('id')->on('users')->nullOnDelete();
            $table->timestamps();
        });
    }
    /** * Reverse the migrations. */ public function down(): void
    {
        Schema::dropIfExists('competitions');
    }
};