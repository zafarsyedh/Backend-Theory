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
        Schema::create('sms_email_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('exam_id');
            $table->foreign('exam_id')->references('id')->on('exam_schedules')->onDelete('restrict')->onUpdate('cascade');
            $table->text('content')->nullable()->default(null);
            $table->enum('type', ['1', '2'])->comment('1 means SMS and 2 means Email');
            $table->enum('is_send', ['1', '2'])->comment('1 means send and 2 means fail');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sms_email_logs');
    }
};
