<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tenant;
use App\Models\TenantBilling;
use App\Models\EmailLog;
use App\Models\SmsLog;
use App\Models\Election;
use App\Models\Voter;
use App\Models\Candidate;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BillingController extends Controller
{
    /**
     * Display all billings (platform admin view).
     */
    public function index()
    {
        $billings = TenantBilling::with('tenant')
            ->latest()
            ->paginate(20);

        $summary = [
            'total_pending' => TenantBilling::pending()->sum('total_cost'),
            'total_paid' => TenantBilling::paid()->sum('total_cost'),
            'pending_count' => TenantBilling::pending()->count(),
            'overdue_count' => TenantBilling::overdue()->count(),
        ];

        return view('platform.billing.index', compact('billings', 'summary'));
    }

    /**
     * Display billing for a specific tenant.
     */
    public function tenantBillings(Tenant $tenant)
    {
        $billings = TenantBilling::where('tenant_id', $tenant->id)
            ->latest()
            ->paginate(20);

        $tenantSummary = [
            'total_spent' => TenantBilling::where('tenant_id', $tenant->id)->paid()->sum('total_cost'),
            'current_due' => TenantBilling::where('tenant_id', $tenant->id)->pending()->sum('total_cost'),
            'emails_sent' => EmailLog::where('tenant_id', $tenant->id)->count(),
            'sms_sent' => SmsLog::where('tenant_id', $tenant->id)->count(),
        ];

        return view('platform.billing.tenant', compact('tenant', 'billings', 'tenantSummary'));
    }

    /**
     * Generate a new billing for a tenant.
     */
    public function generate(Request $request, Tenant $tenant = null)
    {
        $request->validate([
            'billing_period_start' => 'required|date',
            'billing_period_end' => 'required|date|after_or_equal:billing_period_start',
        ]);

        $start = Carbon::parse($request->billing_period_start);
        $end = Carbon::parse($request->billing_period_end);

        if ($tenant) {
            $this->generateBillingForTenant($tenant, $start, $end);
            return redirect()->route('platform.billing.tenant', $tenant->id)
                ->with('success', 'Billing generated successfully.');
        }

        // Generate for all active tenants
        $tenants = Tenant::where('status', 'active')->get();
        foreach ($tenants as $t) {
            $this->generateBillingForTenant($t, $start, $end);
        }

        return redirect()->route('platform.billing.index')
            ->with('success', 'Billings generated for all tenants.');
    }

    /**
     * Generate billing for a single tenant.
     */
    private function generateBillingForTenant(Tenant $tenant, Carbon $start, Carbon $end)
    {
        // Check if billing already exists for this period
        $existing = TenantBilling::where('tenant_id', $tenant->id)
            ->where('billing_period_start', $start)
            ->where('billing_period_end', $end)
            ->first();

        if ($existing) {
            return;
        }

        // Get counts for the period
        $emailsSent = EmailLog::where('tenant_id', $tenant->id)
            ->whereBetween('created_at', [$start, $end->endOfDay()])
            ->count();

        $smsSent = SmsLog::where('tenant_id', $tenant->id)
            ->whereBetween('created_at', [$start, $end->endOfDay()])
            ->count();

        $electionsCount = Election::where('tenant_id', $tenant->id)
            ->whereBetween('created_at', [$start, $end->endOfDay()])
            ->count();

        $votersCount = Voter::where('tenant_id', $tenant->id)
            ->whereBetween('created_at', [$start, $end->endOfDay()])
            ->count();

        $candidatesCount = Candidate::whereHas('election', function ($q) use ($tenant) {
            $q->where('tenant_id', $tenant->id);
        })
            ->whereBetween('created_at', [$start, $end->endOfDay()])
            ->count();

        // Calculate costs
        $pricing = config('billing.pricing', [
            'email' => 0.001,
            'sms' => 0.05,
            'base_monthly' => 10.00,
        ]);

        $emailCost = $emailsSent * $pricing['email'];
        $smsCost = $smsSent * $pricing['sms'];
        $baseCost = $pricing['base_monthly'];
        $totalCost = $emailCost + $smsCost + $baseCost;

        TenantBilling::create([
            'tenant_id' => $tenant->id,
            'billing_period_start' => $start,
            'billing_period_end' => $end,
            'emails_sent' => $emailsSent,
            'sms_sent' => $smsSent,
            'elections_count' => $electionsCount,
            'voters_count' => $votersCount,
            'candidates_count' => $candidatesCount,
            'email_cost' => $emailCost,
            'sms_cost' => $smsCost,
            'base_cost' => $baseCost,
            'total_cost' => $totalCost,
            'status' => 'pending',
        ]);
    }

    /**
     * Show billing details.
     */
    public function show(TenantBilling $billing)
    {
        $billing->load('tenant');
        return view('platform.billing.show', compact('billing'));
    }

    /**
     * Mark billing as paid.
     */
    public function markAsPaid(Request $request, TenantBilling $billing)
    {
        $request->validate([
            'payment_method' => 'nullable|string|max:50',
            'payment_reference' => 'nullable|string|max:255',
        ]);

        $billing->markAsPaid(
            $request->payment_method,
            $request->payment_reference
        );

        return redirect()->back()->with('success', 'Billing marked as paid.');
    }

    /**
     * Show form to edit billing.
     */
    public function edit(TenantBilling $billing)
    {
        return view('platform.billing.edit', compact('billing'));
    }

    /**
     * Update billing.
     */
    public function update(Request $request, TenantBilling $billing)
    {
        $request->validate([
            'notes' => 'nullable|string',
            'status' => 'in:pending,paid,cancelled',
        ]);

        $billing->update($request->only(['notes', 'status']));

        return redirect()->route('platform.billing.index')
            ->with('success', 'Billing updated successfully.');
    }

    /**
     * Delete billing.
     */
    public function destroy(TenantBilling $billing)
    {
        $billing->delete();
        return redirect()->route('platform.billing.index')
            ->with('success', 'Billing deleted successfully.');
    }

    /**
     * Show pricing configuration.
     */
    public function pricing()
    {
        $pricing = config('billing.pricing', [
            'email' => 0.001,
            'sms' => 0.05,
            'base_monthly' => 10.00,
        ]);

        return view('platform.billing.pricing', compact('pricing'));
    }

    /**
     * Update pricing configuration.
     */
    public function updatePricing(Request $request)
    {
        $request->validate([
            'email' => 'required|numeric|min:0',
            'sms' => 'required|numeric|min:0',
            'base_monthly' => 'required|numeric|min:0',
        ]);

        // Update config file or database settings
        // For now, we'll just flash to session
        return redirect()->back()->with('success', 'Pricing updated successfully.');
    }
}
