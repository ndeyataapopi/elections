@extends('layouts.app')

@section('content')
<div class="page-breadcrumb border-bottom">
    <div class="row">
        <div class="col-lg-3 col-md-4 col-xs-12 align-self-center">
            <h5 class="font-medium text-uppercase mb-0">Billing Details</h5>
        </div>
        <div class="col-lg-9 col-md-8 col-xs-12 align-self-center">
            <nav aria-label="breadcrumb" class="mt-2 float-md-right float-left">
                <ol class="breadcrumb mb-0 justify-content-end p-0">
                    <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('platform.billing.index') }}">Billing</a></li>
                    <li class="breadcrumb-item active" aria-current="page">#{{ $billing->id }}</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<div class="page-content container-fluid">
    <div class="row">
        <!-- Billing Info -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-dark text-white d-flex justify-content-between">
                    <h5 class="mb-0">Invoice #{{ $billing->id }}</h5>
                    @if($billing->isPaid())
                        <span class="badge badge-success">PAID</span>
                    @elseif($billing->isOverdue())
                        <span class="badge badge-danger">OVERDUE</span>
                    @else
                        <span class="badge badge-warning">PENDING</span>
                    @endif
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted">Billed To:</h6>
                            <h5>{{ $billing->tenant?->name ?? 'N/A' }}</h5>
                            <p class="text-muted">{{ $billing->tenant?->subdomain ?? '' }}</p>
                        </div>
                        <div class="col-md-6 text-md-right">
                            <h6 class="text-muted">Billing Period:</h6>
                            <h6>{{ $billing->billing_period_start->format('F d, Y') }} - {{ $billing->billing_period_end->format('F d, Y') }}</h6>
                            @if($billing->isPaid())
                                <p class="text-success">
                                    <i class="mdi mdi-check-circle"></i> 
                                    Paid on {{ $billing->paid_at?->format('M d, Y') }}
                                </p>
                            @endif
                        </div>
                    </div>

                    <h6 class="text-uppercase text-muted mb-3">Usage Summary</h6>
                    <table class="table table-bordered">
                        <thead class="thead-light">
                            <tr>
                                <th>Item</th>
                                <th>Quantity</th>
                                <th>Rate</th>
                                <th class="text-right">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Base Platform Fee</td>
                                <td>1 month</td>
                                <td>${{ number_format($billing->base_cost, 2) }}</td>
                                <td class="text-right">${{ number_format($billing->base_cost, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Emails Sent</td>
                                <td>{{ $billing->emails_sent }}</td>
                                <td>${{ number_format(config('billing.pricing.email', 0.001), 3) }} each</td>
                                <td class="text-right">${{ number_format($billing->email_cost, 2) }}</td>
                            </tr>
                            <tr>
                                <td>SMS Sent</td>
                                <td>{{ $billing->sms_sent }}</td>
                                <td>${{ number_format(config('billing.pricing.sms', 0.05), 2) }} each</td>
                                <td class="text-right">${{ number_format($billing->sms_cost, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Elections</td>
                                <td>{{ $billing->elections_count }}</td>
                                <td>-</td>
                                <td class="text-right">Included</td>
                            </tr>
                            <tr>
                                <td>Voters</td>
                                <td>{{ $billing->voters_count }}</td>
                                <td>-</td>
                                <td class="text-right">Included</td>
                            </tr>
                            <tr>
                                <td>Candidates</td>
                                <td>{{ $billing->candidates_count }}</td>
                                <td>-</td>
                                <td class="text-right">Included</td>
                            </tr>
                        </tbody>
                        <tfoot class="font-weight-bold">
                            <tr>
                                <td colspan="3" class="text-right">Total:</td>
                                <td class="text-right">${{ number_format($billing->total_cost, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>

                    @if($billing->notes)
                    <div class="mt-4">
                        <h6 class="text-muted">Notes:</h6>
                        <p>{{ $billing->notes }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Actions</h5>
                </div>
                <div class="card-body">
                    @if($billing->isPending())
                        <form action="{{ route('platform.billing.mark-paid', $billing) }}" method="POST" class="mb-3">
                            @csrf
                            @method('PATCH')
                            <div class="form-group">
                                <label>Payment Method</label>
                                <select name="payment_method" class="form-control">
                                    <option value="cash">Cash</option>
                                    <option value="bank_transfer">Bank Transfer</option>
                                    <option value="mobile_money">Mobile Money</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Reference</label>
                                <input type="text" name="payment_reference" class="form-control" placeholder="Transaction ID">
                            </div>
                            <button type="submit" class="btn btn-success btn-block">
                                <i class="mdi mdi-check"></i> Mark as Paid
                            </button>
                        </form>

                        <form action="{{ route('platform.billing.update', $billing) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="cancelled">
                            <button type="submit" class="btn btn-outline-danger btn-block">
                                <i class="mdi mdi-close"></i> Cancel Invoice
                            </button>
                        </form>
                    @else
                        <div class="alert alert-success">
                            <i class="mdi mdi-check-circle"></i> This invoice has been paid.
                        </div>
                    @endif
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">Tenant Summary</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <li><strong>Name:</strong> {{ $billing->tenant?->name }}</li>
                        <li><strong>Subdomain:</strong> {{ $billing->tenant?->subdomain }}</li>
                        <li><strong>Status:</strong> {{ ucfirst($billing->tenant?->status) }}</li>
                    </ul>
                    <a href="{{ route('platform.billing.tenant', $billing->tenant) }}" class="btn btn-outline-primary btn-sm btn-block">
                        View All Billings
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
