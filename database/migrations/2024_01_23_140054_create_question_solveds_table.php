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
        Schema::create('question_solveds', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('attempt_id');
            $table->foreign('attempt_id')->references('id')->on('attempts')->onDelete('restrict')->onUpdate('cascade');
            $table->unsignedBigInteger('q_id');
            $table->foreign('q_id')->references('id')->on('questions')->onDelete('restrict')->onUpdate('cascade');
            $table->string('choosed_option')->nullable()->default(null);
            $table->enum('is_correct_ans',[0,1,null])->nullable()->default(null);
            $table->enum('is_answered',[0,1])->nullable()->default(0);
            $table->string('q_lnag');
            $table->string('audio_lnag');
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
        Schema::dropIfExists('question_solveds');
    }
};
