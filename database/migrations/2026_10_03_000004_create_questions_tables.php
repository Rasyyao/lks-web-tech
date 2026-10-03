<?php

use App\Enums\QuestionStatus;
use App\Enums\QuestionType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('topic_id')->constrained()->cascadeOnDelete();
            $table->string('type')->default(QuestionType::MultipleChoice->value);
            $table->longText('body_md');
            $table->unsignedTinyInteger('difficulty')->default(1);
            $table->unsignedInteger('points')->default(10);
            $table->longText('explanation_md')->nullable();
            $table->string('status')->default(QuestionStatus::Draft->value);
            $table->unsignedInteger('version')->default(1);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('question_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained()->cascadeOnDelete();
            $table->string('label');
            $table->boolean('is_correct')->default(false);
            $table->unsignedSmallInteger('position')->default(0);
            $table->timestamps();
        });

        Schema::create('question_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained()->cascadeOnDelete();
            $table->string('text');
            $table->string('normalized');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('question_answers');
        Schema::dropIfExists('question_options');
        Schema::dropIfExists('questions');
    }
};
