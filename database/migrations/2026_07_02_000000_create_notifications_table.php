<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('care_seeker_uid');
            $table->string('care_giver_uid');
            $table->integer('match_point');
            $table->timestamps();

            $table->foreign('care_seeker_uid')->references('uid')->on('care_seekers')->onDelete('cascade');
            $table->foreign('care_giver_uid')->references('uid')->on('care_givers')->onDelete('cascade');
        });

        Schema::dropIfExists('ai_recommendations');
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');

        Schema::create('ai_recommendations', function (Blueprint $table) {
            $table->id();
            $table->string('care_seeker_uid');
            $table->string('care_giver_uid');
            $table->integer('match_point');
            $table->timestamps();

            $table->foreign('care_seeker_uid')->references('uid')->on('care_seekers')->onDelete('cascade');
            $table->foreign('care_giver_uid')->references('uid')->on('care_givers')->onDelete('cascade');
        });
    }
};