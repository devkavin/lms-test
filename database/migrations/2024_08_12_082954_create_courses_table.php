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

        // title, description, and category
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('instructor_id')->unsigned();
            $table->foreign('instructor_id')->references('id')->on('users');
            $table->string('title');
            $table->text('description');
            $table->string('category');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
