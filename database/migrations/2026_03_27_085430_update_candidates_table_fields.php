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
        Schema::table('candidates', function (Blueprint $table) {
            // Add new fields for candidate upload
            // $table->string('staff_number')->nullable()->after('id');
            // $table->string('first_name')->nullable()->after('staff_number');
            // $table->string('last_name')->nullable()->after('first_name');
            // $table->string('gender')->nullable()->after('last_name');
            // $table->string('phone')->nullable()->after('email');
            // $table->boolean('profile_complete')->default(false);
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('candidates', function (Blueprint $table) {
            // Drop new fields
            $table->dropColumn(['staff_number', 'first_name', 'last_name', 'gender', 'phone', 'profile_complete']);
            
        });
    }
};
