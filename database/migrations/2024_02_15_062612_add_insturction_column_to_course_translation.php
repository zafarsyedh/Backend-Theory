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
        Schema::table('course_translations', function (Blueprint $table) {
            $table->string('instructions')->nullable()->default(null)->after('full_name');
            $table->string('video_link')->nullable()->default(null)->after('instructions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_translations', function (Blueprint $table) {
            //
        });
    }
};
