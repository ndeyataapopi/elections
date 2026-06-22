@extends('layouts.app')

@section('content')
<div class="page-breadcrumb border-bottom">
    <div class="row">
        <div class="col-lg-3 col-md-4 col-xs-12 align-self-center">
            <h5 class="font-medium text-uppercase mb-0">Billing Management</h5>
        </div>
        <div class="col-lg-9 col-md-8 col-xs-12 align-self-center">
            <nav aria-label="breadcrumb" class="mt-2 float-md-right float-left">
                <ol class="breadcrumb mb-0 justify-content-end p-0">
                    <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Billing</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<div class="page-content container-fluid">
    <!-- Summary Cards -->
    <div class="row">
        <div class="col-md-3">
            <div class="card bg-light-warning">
                <div class="card-body">
                    <h5 class="card-title text-uppercase text-warning">Pending</h5>
                    <h3 class="mb-0 font-weight-bold">${{ number_format($summary['total_pending'], 2) }}</h3>
                    <small class="text-muted">{{ $summary['pending_count'] }} invoices</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-light-success">
                <div class="card-body">
                    <h5 class="card-title text-uppercase text-success">Total Paid</h5>
                    <h3 class="mb-0 font-weight-bold">${{ number_format($summary['total_paid'], 2) }}</h3>
                    <small class="text-muted">All time revenue</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-light-danger">
                <div class="card-body">
                    <h5 class="card-title text-uppercase text-danger">Overdue</h5>
                    <h3 class="mb-0 font-weight-bold">{{ $summary['overdue_count'] }}</h3>
                    <small class="text-muted"> invoices overdue</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-light-info">
                <div class="card-body">
                    <h5 class="card-title text-uppercase text-info">Actions</h5>
                    <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#generateModal">
                        <i class="mdi mdi-plus"></i> Generate Billing
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Billings Table -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title text-uppercase">All Billings</h5>
                    <div class="table-responsive mt-3">
                        <table class="table table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>Tenant</th>
                                    <th>Period</th>
                                    <th>Usage</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($billings as $billing)
                                <tr>
                                    <td>
                                        <strong>{{ $billing->tenant?->name ?? 'N/A' }}</strong>
                                    </td>
                                    <td>
                                        {{ $billing->billing_period_start->format('M d') }} - 
                                        {{ $billing->billing_period_end->format('M d, Y') }}
                                    </td>
                                    <td>
                                        <small>
                                            <i class="mdi mdi-email text-info"></i> {{ $billing->emails_sent }}
                                            <i class="mdi mdi-message-text text-success ml-2"></i> {{ $billing->sms_sent }}
                                        </small>
                                    </td>
                                    <td>
                                        <strong>${{ number_format($billing->total_cost, 2) }}</strong>
                                    </td>
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
                                        @if($billing->isPending())
                                        <form action="{{ route('platform.billing.mark-paid', $billing) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-success">
                                                <i class="mdi mdi-check"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">No billings found</td>
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

<!-- Generate Billing Modal -->
<div class="modal fade" id="generateModal" tabindex="-1" role="dialog" aria-labelledby="generateModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('platform.billing.generate') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="generateModalLabel">Generate Billing</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
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
                    <button type="submit" class="btn btn-primary">Generate for All Tenants</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
