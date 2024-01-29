<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('question_translations', function (Blueprint $table) {
            $table->dropColumn('q_audio');
            $table->dropColumn('opt_a_audio');
            $table->dropColumn('opt_b_audio');
            $table->dropColumn('opt_c_audio');
        });

        Schema::table('question_translations', function (Blueprint $table) {
            $table->string('opt_a_audio')->default(null)->nullable();
            $table->string('opt_b_audio')->default(null)->nullable();
            $table->string('opt_c_audio')->default(null)->nullable();
            $table->string('q_audio')->default(null)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('question_translations', function (Blueprint $table) {
            //
        });
    }
};
