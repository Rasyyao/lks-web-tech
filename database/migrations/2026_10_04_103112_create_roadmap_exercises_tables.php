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
        Schema::create('exercises', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('level_slug')->index();
            $table->string('title');
            $table->string('type')->default('function'); // function, dom, manual
            $table->longText('instructions');
            $table->text('starter_code')->nullable();
            $table->json('test_cases')->nullable();
            $table->boolean('is_required')->default(true);
            $table->unsignedInteger('time_limit_ms')->default(2000);
            $table->unsignedInteger('position')->default(0);
            $table->string('hash', 64)->nullable();
            $table->timestamps();
        });

        Schema::create('exercise_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('exercise_id')->constrained('exercises')->cascadeOnDelete();
            $table->string('exercise_slug')->index();
            $table->mediumText('code');
            $table->json('results')->nullable();
            $table->boolean('passed')->default(false);
            $table->unsignedInteger('duration_ms')->default(0);
            $table->boolean('verified')->default(false);
            $table->timestamps();

            $table->index(['user_id', 'exercise_slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exercise_attempts');
        Schema::dropIfExists('exercises');
    }
};
