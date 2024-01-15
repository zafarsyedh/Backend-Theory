<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfigurationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('configurations', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable()->default(null);
            $table->string('logo')->nullable()->default(null);
            $table->string('admin_bg_img')->nullable()->default(null);
            $table->string('std_bg_img')->nullable()->default(null);
            $table->boolean('enable_email')->nullable()->default(null);
            $table->string('e_host')->nullable()->default(null);
            $table->string('e_user_name')->nullable()->default(null);
            $table->string('e_password')->nullable()->default(null);
            $table->string('e_port')->nullable()->default(null);
            $table->string('smtp_secure')->nullable()->default(null);
            $table->string('email_template')->nullable()->default(null);
            $table->boolean('enable_sms')->nullable()->default(null);
            $table->string('sms_template')->nullable()->default(null);
            $table->boolean('is_deleted')->default(0);
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
        Schema::dropIfExists('configurations');
    }
}
