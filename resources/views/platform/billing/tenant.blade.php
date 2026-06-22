@extends('layouts.app')

@section('content')
<div class="page-breadcrumb border-bottom">
    <div class="row">
        <div class="col-lg-3 col-md-4 col-xs-12 align-self-center">
            <h5 class="font-medium text-uppercase mb-0">Tenant Billing</h5>
        </div>
        <div class="col-lg-9 col-md-8 col-xs-12 align-self-center">
            <nav aria-label="breadcrumb" class="mt-2 float-md-right float-left">
                <ol class="breadcrumb mb-0 justify-content-end p-0">
                    <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('platform.billing.index') }}">Billing</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $tenant->name }}</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<div class="page-content container-fluid">
    <!-- Tenant Info & Summary -->
    <div class="row">
        <div class="col-md-4">
            <div class="card bg-light">
                <div class="card-body">
                    <h5 class="card-title">{{ $tenant->name }}</h5>
                    <p class="text-muted">{{ $tenant->subdomain }}</p>
                    <hr>
                    <div class="row text-center">
                        <div class="col-6">
                            <h4 class="text-success">${{ number_format($tenantSummary['total_spent'], 2) }}</h4>
                            <small class="text-muted">Total Paid</small>
                        </div>
                        <div class="col-6">
                            <h4 class="text-warning">${{ number_format($tenantSummary['current_due'], 2) }}</h4>
                            <small class="text-muted">Current Due</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title text-uppercase">Usage Statistics</h5>
                    <div class="row mt-3">
                        <div class="col-md-3 text-center">
                            <h3 class="text-info">{{ number_format($tenantSummary['emails_sent']) }}</h3>
                            <small>Emails Sent</small>
                        </div>
                        <div class="col-md-3 text-center">
                            <h3 class="text-success">{{ number_format($tenantSummary['sms_sent']) }}</h3>
                            <small>SMS Sent</small>
                        </div>
                        <div class="col-md-3 text-center">
                            <h3 class="text-primary">{{ $tenant->elections()->count() }}</h3>
                            <small>Total Elections</small>
                        </div>
                        <div class="col-md-3 text-center">
                            <h3 class="text-warning">{{ $tenant->voters()->count() }}</h3>
                            <small>Total Voters</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Billings Table -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Billing History</h5>
                    <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#generateModal">
                        <i class="mdi mdi-plus"></i> Generate New
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>Period</th>
                                    <th>Emails</th>
                                    <th>SMS</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($billings as $billing)
                                <tr>
                                    <td>
                                        {{ $billing->billing_period_start->format('M d') }} - 
                                        {{ $billing->billing_period_end->format('M d, Y') }}
                                    </td>
                                    <td>{{ $billing->emails_sent }}</td>
                                    <td>{{ $billing->sms_sent }}</td>
                                    <td><strong>${{ number_format($billing->total_cost, 2) }}</strong></td>
                                    <td>
                                        @if($billing->isPaid())
                                            <span class="badge badge-success">Paid</span>
                                        @elseif($billing->isOverdue())
                                            <span class="badge badge-danger">Overdue</span>
                                        @else
                                            <span class="badge badge-warning">Pending</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('platform.billing.show', $billing) }}" class="btn btn-sm btn-info">
                                            <i class="mdi mdi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">No billing history</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $billings->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Generate Modal -->
<div class="modal fade" id="generateModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('platform.billing.generate.tenant', $tenant) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Generate Billing for {{ $tenant->name }}</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Billing Period Start</label>
                        <input type="date" name="billing_period_start" class="form-control" required 
                               value="{{ now()->startOfMonth()->format('Y-m-d') }}">
                    </div>
                    <div class="form-group">
                        <label>Billing Period End</label>
                        <input type="date" name="billing_period_end" class="form-control" required
                               value="{{ now()->endOfMonth()->format('Y-m-d') }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Generate</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
