<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->text('message')->nullable()->after('match_point');
            $table->string('type')->nullable()->after('message');
            $table->boolean('is_read')->default(true)->after('type');
             $table->string('user_uid')->nullable()->after('is_read');

            $table->foreign('user_uid')->references('uid')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropForeign(['user_uid']);
            $table->dropColumn(['message', 'type', 'is_read', 'user_uid']);
        });
    }
};