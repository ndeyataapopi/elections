<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenantBilling extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'billing_period_start',
        'billing_period_end',
        'emails_sent',
        'sms_sent',
        'elections_count',
        'voters_count',
        'candidates_count',
        'email_cost',
        'sms_cost',
        'base_cost',
        'total_cost',
        'status',
        'paid_at',
        'payment_method',
        'payment_reference',
        'notes',
    ];

    protected $casts = [
        'billing_period_start' => 'date',
        'billing_period_end' => 'date',
        'paid_at' => 'datetime',
        'emails_sent' => 'integer',
        'sms_sent' => 'integer',
        'elections_count' => 'integer',
        'voters_count' => 'integer',
        'candidates_count' => 'integer',
        'email_cost' => 'decimal:2',
        'sms_cost' => 'decimal:2',
        'base_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'pending')
                     ->where('billing_period_end', '<', now()->subDays(30));
    }

    public function markAsPaid(string $paymentMethod = null, string $paymentReference = null): void
    {
        $this->update([
            'status' => 'paid',
            'paid_at' => now(),
            'payment_method' => $paymentMethod,
            'payment_reference' => $paymentReference,
        ]);
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isOverdue(): bool
    {
        return $this->isPending() && $this->billing_period_end->addDays(30)->isPast();
    }
}
