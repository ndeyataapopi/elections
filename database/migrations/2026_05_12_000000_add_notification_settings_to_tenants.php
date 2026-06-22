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
        Schema::table('tenants', function (Blueprint $table) {
            // Email notification settings
            $table->boolean('enable_candidate_email_notifications')->default(true)->after('status');
            $table->boolean('enable_voter_email_notifications')->default(true)->after('enable_candidate_email_notifications');

            // SMS notification settings
            $table->boolean('enable_candidate_sms_notifications')->default(true)->after('enable_voter_email_notifications');
            $table->boolean('enable_voter_sms_notifications')->default(true)->after('enable_candidate_sms_notifications');

            // Notification templates (optional, can be customized per tenant)
            $table->text('candidate_email_template')->nullable()->after('enable_voter_sms_notifications');
            $table->text('candidate_sms_template')->nullable()->after('candidate_email_template');
            $table->text('voter_email_template')->nullable()->after('candidate_sms_template');
            $table->text('voter_sms_template')->nullable()->after('voter_email_template');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn([
                'enable_candidate_email_notifications',
                'enable_voter_email_notifications',
                'enable_candidate_sms_notifications',
                'enable_voter_sms_notifications',
                'candidate_email_template',
                'candidate_sms_template',
                'voter_email_template',
                'voter_sms_template',
            ]);
        });
    }
};
