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
        Schema::create('roadmap_pages', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('kind')->default('level'); // level, reference, guide
            $table->unsignedInteger('position')->default(0);
            $table->string('title');
            $table->text('goal')->nullable();
            $table->string('estimated_time')->nullable();
            $table->string('content_hash', 64)->nullable();
            $table->timestamps();
        });

        Schema::create('roadmap_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('roadmap_page_id')->constrained('roadmap_pages')->cascadeOnDelete();
            $table->string('slug');
            $table->string('kind')->default('materi'); // materi, latihan, cek_hasil, dipakai_di, overview
            $table->string('title');
            $table->unsignedInteger('position')->default(0);
            $table->longText('body_md')->nullable();
            $table->longText('body_html')->nullable();
            $table->string('hash', 64)->nullable();
            $table->timestamps();

            $table->unique(['roadmap_page_id', 'slug']);
        });

        Schema::create('roadmap_checkpoints', function (Blueprint $table) {
            $table->id();
            $table->foreignId('roadmap_page_id')->constrained('roadmap_pages')->cascadeOnDelete();
            $table->string('slug');
            $table->unsignedInteger('position')->default(0);
            $table->text('prompt');
            $table->timestamps();

            $table->unique(['roadmap_page_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roadmap_checkpoints');
        Schema::dropIfExists('roadmap_sections');
        Schema::dropIfExists('roadmap_pages');
    }
};
