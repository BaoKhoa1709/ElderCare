<?php

use App\Enums\Role;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('care_givers', function (Blueprint $table) {
            $table->string('uid')->primary();
            $table->string('user_uid');
            $table->date('dob')->nullable(false);
            $table->string('phone_number')->nullable(false);
            $table->integer('year_experience')->nullable(false);
            $table->decimal('fee', 10, 2)->nullable(false);
            $table->text('bio')->nullable(false);
            $table->string('image_url')->nullable();
            $table->timestamps();

            $table->foreign('user_uid')->references('uid')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('care_givers');
    }
};
