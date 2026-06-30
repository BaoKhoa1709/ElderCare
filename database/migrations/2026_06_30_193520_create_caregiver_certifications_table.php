<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('caregiver_certifications', function (Blueprint $table) {
            $table->id();
            $table->string('care_giver_uid');
            $table->string('certificate_name')->nullable(false);
            $table->string('issuer')->nullable(false);
            $table->date('issue_date')->nullable(false);
            $table->date('expiration_date')->nullable(false);
            $table->timestamps();

            $table->foreign('care_giver_uid')->references('uid')->on('care_givers')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('caregiver_certifications');
    }
};
