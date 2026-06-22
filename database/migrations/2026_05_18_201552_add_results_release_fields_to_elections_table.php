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
        Schema::table('elections', function (Blueprint $table) {
            // Use string instead of enum for  compatibility
            $table->string('results_status', 20)
                  ->default('pending')
                  ->after('status');
            $table->timestamp('results_released_at')->nullable()->after('results_status');
            $table->unsignedBigInteger('results_released_by')->nullable()->after('results_released_at');
            $table->timestamp('results_approved_at')->nullable()->after('results_released_by');
            $table->text('results_approval_notes')->nullable()->after('results_approved_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('elections', function (Blueprint $table) {
            $table->dropColumn([
                'results_status',
                'results_released_at',
                'results_released_by',
                'results_approved_at',
                'results_approval_notes',
            ]);
        });
    }
};
