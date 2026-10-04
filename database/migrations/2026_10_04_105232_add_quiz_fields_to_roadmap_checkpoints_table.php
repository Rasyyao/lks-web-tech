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
        Schema::table('roadmap_checkpoints', function (Blueprint $table) {
            $table->json('options')->nullable()->after('prompt');
            $table->string('correct_answer', 16)->nullable()->after('options');
            $table->text('explanation')->nullable()->after('correct_answer');
        });

        Schema::table('checkpoint_marks', function (Blueprint $table) {
            $table->string('selected_answer', 16)->nullable()->after('checkpoint_slug');
        });
    }

    public function down(): void
    {
        Schema::table('checkpoint_marks', function (Blueprint $table) {
            $table->dropColumn('selected_answer');
        });

        Schema::table('roadmap_checkpoints', function (Blueprint $table) {
            $table->dropColumn(['options', 'correct_answer', 'explanation']);
        });
    }
};
