<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('care_seekers', function (Blueprint $table) {
            $table->string('uid')->primary();
            $table->string('user_uid');
            $table->date('dob')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('preferred_giver_gender')->nullable();
            $table->timestamps();

            $table->foreign('user_uid')->references('uid')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('care_seekers');
    }
};
