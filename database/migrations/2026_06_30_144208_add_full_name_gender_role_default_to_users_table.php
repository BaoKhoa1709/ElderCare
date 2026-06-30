<?php

use App\Enums\Role;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('full_name')->nullable()->after('uid');
            $table->string('gender')->default('MALE')->after('full_name');
            $table->string('role')->default(Role::USER->value)->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['full_name', 'gender']);
            $table->string('role')->default(Role::SEEKER->value)->change();
        });
    }
};
