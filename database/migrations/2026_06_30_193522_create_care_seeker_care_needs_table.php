<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('care_seeker_care_needs', function (Blueprint $table) {
            $table->string('care_seeker_uid');
            $table->string('care_need');
            $table->timestamps();

            $table->primary(['care_seeker_uid', 'care_need']);
            $table->foreign('care_seeker_uid')->references('uid')->on('care_seekers')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('care_seeker_care_needs');
    }
};
