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
        Schema::table('course_configrations', function (Blueprint $table) {
            $table->string('p_specific_question');
            $table->string('p_common_question');
            $table->string('p_video_question');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_configrations', function (Blueprint $table) {
            //
        });
    }
};
