<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course_configrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->references('id')->on('courses')->onDelete('cascade')->onUpdate('cascade');
            $table->bigInteger('specific_question');
            $table->bigInteger('common_question');
            $table->bigInteger('video_question');
            $table->bigInteger('require_type');
            $table->bigInteger('specific_require')->nullable();
            $table->bigInteger('common_require')->nullable();
            $table->bigInteger('video_require')->nullable();
            $table->bigInteger('total_require')->nullable();
            $table->bigInteger('total_duration');
            $table->bigInteger('video_duration');
            $table->softDeletes();
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('course_configrations');
    }
};
