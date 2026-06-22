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
        Schema::create('tenant_billings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->date('billing_period_start');
            $table->date('billing_period_end');
            $table->unsignedInteger('emails_sent')->default(0);
            $table->unsignedInteger('sms_sent')->default(0);
            $table->unsignedInteger('elections_count')->default(0);
            $table->unsignedInteger('voters_count')->default(0);
            $table->unsignedInteger('candidates_count')->default(0);
            $table->decimal('email_cost', 10, 2)->default(0);
            $table->decimal('sms_cost', 10, 2)->default(0);
            $table->decimal('base_cost', 10, 2)->default(0);
            $table->decimal('total_cost', 10, 2)->default(0);
            $table->string('status', 20)->default('pending'); // pending, paid, cancelled
            $table->timestamp('paid_at')->nullable();
            $table->string('payment_method', 50)->nullable();
            $table->string('payment_reference')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'status']);
            $table->index(['billing_period_start', 'billing_period_end']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenant_billings');
    }
};
