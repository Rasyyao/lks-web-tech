<?php

use App\Enums\ModuleStatus;
use App\Enums\ModuleTrack;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('modules', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title');
            $table->string('track')->default(ModuleTrack::Fullstack->value);
            $table->unsignedTinyInteger('level')->default(1);
            $table->text('summary')->nullable();
            $table->longText('brief_md')->nullable();
            $table->longText('rules_md')->nullable();
            $table->unsignedInteger('duration_minutes')->nullable();
            $table->timestamp('opens_at')->nullable();
            $table->timestamp('closes_at')->nullable();
            $table->string('status')->default(ModuleStatus::Draft->value);
            $table->unsignedSmallInteger('max_attempts_per_day')->default(5);
            $table->unsignedInteger('version')->default(1);
            $table->string('rubric_ref')->nullable(); // For R2
            $table->timestamps();
        });

        Schema::create('module_assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_id')->constrained()->cascadeOnDelete();
            $table->string('label');
            $table->string('path');
            $table->unsignedBigInteger('size')->default(0);
            $table->timestamps();
        });

        Schema::create('module_cohort', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cohort_id')->constrained()->cascadeOnDelete();

            $table->unique(['module_id', 'cohort_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('module_cohort');
        Schema::dropIfExists('module_assets');
        Schema::dropIfExists('modules');
    }
};
