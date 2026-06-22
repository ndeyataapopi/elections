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
        Schema::create('sms_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('recipient_type'); // 'candidate', 'voter'
            $table->unsignedBigInteger('recipient_id'); // candidate_id or voter_id
            $table->string('recipient_phone');
            $table->string('recipient_name')->nullable();
            $table->string('sms_type'); // 'profile_update', 'voting_link', etc.
            $table->text('message');
            $table->foreignId('election_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('status')->default('pending'); // pending, sent, failed
            $table->text('provider_response')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sms_logs');
    }
};
