<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('caregiver_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('care_giver_uid');
            $table->json('day_of_weeks')->nullable();
            $table->time('start_time');
            $table->time('end_time');
            $table->timestamps();

            $table->foreign('care_giver_uid')->references('uid')->on('care_givers')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('caregiver_schedules');
    }
};
