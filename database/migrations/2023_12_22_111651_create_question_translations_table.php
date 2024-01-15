<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestionTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('question_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('q_id');
            $table->foreign('q_id')->references('id')->on('questions')->onDelete('restrict')->onUpdate('cascade');
            $table->unsignedBigInteger('lang_id');
            $table->foreign('lang_id')->references('id')->on('languages')->onDelete('restrict')->onUpdate('cascade');
            $table->string('q_title');
            $table->string('opt_a')->default()->nullable();
            $table->string('opt_b')->default()->nullable();
            $table->string('opt_c')->default()->nullable();
            $table->string('opt_a_image')->default()->nullable();
            $table->string('opt_b_image')->default()->nullable();
            $table->string('opt_c_image')->default()->nullable();
            $table->string('opt_a_audio')->default()->nullable();
            $table->string('opt_b_audio')->default()->nullable();
            $table->string('opt_c_audio')->default()->nullable();
            $table->string('lang');
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
        Schema::dropIfExists('question_translations');
    }
}
