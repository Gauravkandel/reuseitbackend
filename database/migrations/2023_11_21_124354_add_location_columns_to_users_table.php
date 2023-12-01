<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('Province')->nullable();
            $table->string('District')->nullable();
            $table->string('Municipality')->nullable();
            $table->string('Phone_no')->nullable();
            $table->string('Profile_image')->default("Default_profile.jpg");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('Province');
            $table->dropColumn('District');
            $table->dropColumn('Municipality');
            $table->dropColumn('Phone_no');
            $table->dropColumn('Profile_image');
        });
    }
};
