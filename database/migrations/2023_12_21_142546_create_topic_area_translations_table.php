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
        Schema::create('topic_area_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('topic_area_id')->references('id')->on('topic_areas')->onDelete('cascade');
            $table->string('full_name');
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
        Schema::dropIfExists('topic_area_translations');
    }
};
