<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('practice_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->json('filters')->nullable();
            $table->unsignedSmallInteger('question_count')->default(0);
            $table->unsignedInteger('time_limit_seconds')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->unsignedInteger('points_earned')->default(0);
            $table->decimal('score_pct', 5, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('practice_attempt_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attempt_id')->constrained('practice_attempts')->cascadeOnDelete();
            $table->foreignId('question_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('question_version')->default(1);
            $table->json('snapshot');
            $table->json('answer')->nullable();
            $table->boolean('is_correct')->nullable();
            $table->unsignedInteger('points')->default(0);
            $table->unsignedSmallInteger('position')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('practice_attempt_items');
        Schema::dropIfExists('practice_attempts');
    }
};
