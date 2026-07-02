<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('care_location');
            $table->date('from_date');
            $table->integer('duration');
            $table->string('status');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->string('care_seeker_uid');
            $table->string('care_giver_uid');
            $table->text('note')->nullable();
            $table->string('meeting_link')->nullable();
            $table->string('payment');
            $table->timestamps();

            $table->foreign('care_seeker_uid')->references('uid')->on('care_seekers')->onDelete('cascade');
            $table->foreign('care_giver_uid')->references('uid')->on('care_givers')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
