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
        Schema::create('practice_results', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('std_id');
            $table->foreign('std_id')->references('id')->on('students')->onDelete('restrict')->onUpdate('cascade');
            $table->string('traffic_id');
            $table->unsignedBigInteger('exam_id');
            $table->foreign('exam_id')->references('id')->on('exam_schedules')->onDelete('restrict')->onUpdate('cascade');
            $table->unsignedInteger('require_percentage');
            $table->unsignedInteger('obtain_percentage');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('practice_results');
    }
};
