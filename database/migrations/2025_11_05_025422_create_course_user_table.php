<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('course_user', function (Blueprint $table) {
            $table->id();
            // Kunci asing untuk User
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            // Kunci asing untuk Course
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            // Opsional: Pastikan kombinasi user & course unik
            $table->unique(['user_id', 'course_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_user');
    }
};