<?php

use App\Enums\SubmissionStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('module_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('attempt_no')->default(1);
            $table->string('path');
            $table->string('original_name');
            $table->unsignedBigInteger('size')->default(0);
            $table->string('sha256', 64);
            $table->string('status')->default(SubmissionStatus::Received->value);
            $table->unsignedTinyInteger('manual_score')->nullable();
            $table->longText('feedback_md')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'module_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('submissions');
    }
};
