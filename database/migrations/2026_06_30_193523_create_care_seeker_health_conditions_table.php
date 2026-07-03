<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('care_seeker_health_conditions', function (Blueprint $table) {
            $table->string('care_seeker_uid');
            $table->string('health_condition');
            $table->timestamps();

            $table->primary(['care_seeker_uid', 'health_condition']);
            $table->foreign('care_seeker_uid')->references('uid')->on('care_seekers')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('care_seeker_health_conditions');
    }
};
