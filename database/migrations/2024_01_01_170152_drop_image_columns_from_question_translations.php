<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('question_translations', function (Blueprint $table) {

            DB::statement('ALTER TABLE question_translations DROP COLUMN opt_a_image');
            DB::statement('ALTER TABLE question_translations DROP COLUMN opt_b_image');
            DB::statement('ALTER TABLE question_translations DROP COLUMN opt_c_image');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('question_translations', function (Blueprint $table) {

        });
    }
};
