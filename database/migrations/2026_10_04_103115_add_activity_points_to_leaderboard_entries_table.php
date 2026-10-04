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
        Schema::table('leaderboard_entries', function (Blueprint $table) {
            $table->unsignedInteger('activity_points')->default(0)->after('module_points');
            $table->json('breakdown')->nullable()->after('total');
        });
    }

    public function down(): void
    {
        Schema::table('leaderboard_entries', function (Blueprint $table) {
            $table->dropColumn(['activity_points', 'breakdown']);
        });
    }
};
