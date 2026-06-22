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
        Schema::table('voters', function (Blueprint $table) {
            $table->string('staff_number')->nullable()->after('election_id');
            $table->string('first_name')->nullable()->after('staff_number');
            $table->string('last_name')->nullable()->after('first_name');
            $table->string('gender')->nullable()->after('last_name');
            $table->string('plain_token')->nullable()->after('token');
            $table->dropColumn('name');
            $table->dropColumn('token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('voters', function (Blueprint $table) {
            $table->dropColumn(['staff_number', 'first_name', 'last_name', 'gender', 'plain_token']);
            $table->string('name')->nullable();
            $table->string('token')->nullable();
        });
    }
};
