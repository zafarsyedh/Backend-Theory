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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('traffic_id');
            $table->string('std_name')->nullable();
            $table->string('email');
            $table->string('password');
            $table->string('std_gender')->nullable();
            $table->string('geartype')->nullable();
            $table->string('language')->nullable();
            $table->string('branch')->nullable();
            $table->string('mobile_no')->nullable();
            $table->string('appltype')->nullable();
            $table->string('progress')->nullable();
            $table->string('brcode')->nullable();
            $table->string('coursetype')->nullable();
            $table->string('prefferd_golden_chance')->nullable();
            $table->string('upcomingcls')->nullable();
            $table->string('historycls')->nullable();
            $table->string('pendingamount')->nullable();
            $table->string('paidamount')->nullable();
            $table->softDeletes();
            $table->rememberToken();
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
        Schema::dropIfExists('students');
    }
};
