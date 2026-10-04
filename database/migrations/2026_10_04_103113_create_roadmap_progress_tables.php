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
        Schema::create('section_reads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('roadmap_section_id')->constrained('roadmap_sections')->cascadeOnDelete();
            $table->string('section_slug')->index();
            $table->timestamp('read_at')->useCurrent();
            $table->timestamps();

            $table->unique(['user_id', 'section_slug']);
        });

        Schema::create('checkpoint_marks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('roadmap_checkpoint_id')->constrained('roadmap_checkpoints')->cascadeOnDelete();
            $table->string('checkpoint_slug')->index();
            $table->boolean('is_understood')->default(true);
            $table->timestamp('marked_at')->useCurrent();
            $table->timestamps();

            $table->unique(['user_id', 'checkpoint_slug']);
        });

        Schema::create('level_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('level_slug')->index();
            $table->unsignedInteger('sections_total')->default(0);
            $table->unsignedInteger('sections_read')->default(0);
            $table->unsignedInteger('checkpoints_total')->default(0);
            $table->unsignedInteger('checkpoints_marked')->default(0);
            $table->unsignedInteger('exercises_total')->default(0);
            $table->unsignedInteger('exercises_passed')->default(0);
            $table->unsignedInteger('percent_complete')->default(0);
            $table->boolean('is_completed')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'level_slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('level_progress');
        Schema::dropIfExists('checkpoint_marks');
        Schema::dropIfExists('section_reads');
    }
};
