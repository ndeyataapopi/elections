@extends('layouts.app')

@section('title', 'Election Results - ' . $election->name)

@section('content')
<!-- ============================================================== -->
<!-- Bread crumb and right sidebar toggle -->
<!-- ============================================================== -->
<div class="page-breadcrumb border-bottom">
    <div class="row">
        <div class="col-lg-3 col-md-4 col-xs-12 align-self-center">
            <h5 class="font-medium text-uppercase mb-0">Election Results</h5>
        </div>
        <div class="col-lg-9 col-md-8 col-xs-12 align-self-center">
            <nav aria-label="breadcrumb" class="mt-2 float-md-right float-left">
                <ol class="breadcrumb mb-0 justify-content-end p-0">
                    <li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('reports.index') }}">Reports</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $election->name }}</li>
                </ol>
            </nav>
        </div>
    </div>
</div>
<!-- ============================================================== -->
<!-- End Bread crumb and right sidebar toggle -->
<!-- ============================================================== -->

<!-- ============================================================== -->
<!-- Container fluid  -->
<!-- ============================================================== -->
<div class="page-content container-fluid">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            {{ session('warning') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- Election Info Card -->
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title text-uppercase mb-0">
                        {{ $election->name }}
                        <span class="pull-right">
                            <a class="btn btn-dark btn-sm" href="{{ route('reports.index') }}">
                            ← Back to Reports </a>
                            @if(in_array($election->results_status, ['released', 'approved', 'rejected']))
                            <a class="btn btn-danger btn-sm ml-2" href="{{ route('reports.export.pdf', $election->id) }}">
                                <i class="mdi mdi-file-pdf"></i> PDF</a>
                            <a class="btn btn-success btn-sm ml-2" href="{{ route('reports.export.csv', $election->id) }}">
                                <i class="mdi mdi-file-excel"></i> CSV</a>
                            @endif
                        </span>
                    </h4><br>
                    <h5 class="card-subtitle">Election Results Summary</h5>

                    <div class="row mt-4">
                        <div class="col-md-3 text-center">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h2 class="text-primary">{{ $results['total_voters'] }}</h2>
                                    <p class="text-muted mb-0">Total Registered Voters</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h2 class="text-success">{{ $results['total_votes_cast'] }}</h2>
                                    <p class="text-muted mb-0">Votes Cast</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h2 class="text-info">{{ $results['turnout_percentage'] }}</h2>
                                    <p class="text-muted mb-0">Voter Turnout</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h2 class="text-warning">{{ $results['total_portfolios'] }}</h2>
                                    <p class="text-muted mb-0">Portfolios</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Results Status Section -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card border-left-{{ $election->results_status === 'approved' ? 'success' : ($election->results_status === 'rejected' ? 'danger' : ($election->results_status === 'released' ? 'warning' : 'secondary')) }}">
                                <div class="card-body">
                                    <h5 class="card-title">Results Status</h5>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            @switch($election->results_status)
                                                @case('pending')
                                                    <span class="badge badge-secondary badge-lg" style="font-size: 1.2rem;">Pending Release</span>
                                                    <p class="mt-2 text-muted">Results have not been released yet. Election must be completed before releasing results.</p>
                                                    @break
                                                @case('released')
                                                    <span class="badge badge-warning badge-lg" style="font-size: 1.2rem;">Released - Awaiting Approval</span>
                                                    <p class="mt-2 text-muted">Results have been released to approval administrators on {{ $election->results_released_at?->format('F d, Y H:i') }}.</p>
                                                    @if($election->resultsReleasedBy)
                                                        <p class="text-muted">Released by: {{ $election->resultsReleasedBy->name ?? 'Unknown' }}</p>
                                                    @endif
                                                    @break
                                                @case('approved')
                                                    <span class="badge badge-success badge-lg" style="font-size: 1.2rem;">✓ Approved and Certified</span>
                                                    <p class="mt-2 text-muted">Results have been approved as free and fair on {{ $election->results_approved_at?->format('F d, Y H:i') }}.</p>
                                                    @if($election->results_approval_notes)
                                                        <p class="text-muted"><strong>Notes:</strong> {{ $election->results_approval_notes }}</p>
                                                    @endif
                                                    @break
                                                @case('rejected')
                                                    <span class="badge badge-danger badge-lg" style="font-size: 1.2rem;">✗ Rejected</span>
                                                    <p class="mt-2 text-muted">Results have been rejected and need to be reviewed.</p>
                                                    @if($election->results_approval_notes)
                                                        <p class="text-muted"><strong>Rejection Reason:</strong> {{ $election->results_approval_notes }}</p>
                                                    @endif
                                                    @break
                                            @endswitch
                                        </div>
                                        <div>
                                            @if($election->canReleaseResults())
                                                <form action="{{ route('reports.release', $election->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to release these election results to approval administrators?');">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success btn-lg">
                                                        <i class="fas fa-paper-plane"></i> Release Results
                                                    </button>
                                                </form>
                                            @elseif($election->results_status === 'released')
                                                <a href="{{ route('reports.approve.view', $election->id) }}" class="btn btn-warning btn-lg">
                                                    <i class="fas fa-check-circle"></i> Review & Approve
                                                </a>
                                            @elseif($election->results_status === 'approved')
                                                <button class="btn btn-success btn-lg" disabled>
                                                    <i class="fas fa-check"></i> Certified
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Approval Admins -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <h5>Approval Administrators</h5>
                            @if($election->approvalAdmins->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($election->approvalAdmins as $admin)
                                                <tr>
                                                    <td>{{ $admin->user?->name ?? 'Unknown' }}</td>
                                                    <td>{{ $admin->user?->email ?? 'N/A' }}</td>
                                                    <td>
                                                        @if($election->results_status === 'approved')
                                                            <span class="badge badge-success">Approved</span>
                                                        @elseif($election->results_status === 'released')
                                                            <span class="badge badge-warning">Pending Approval</span>
                                                        @else
                                                            <span class="badge badge-secondary">-</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    No approval administrators configured. Please add approval administrators before releasing results.
                                </div>
                            @endif
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Portfolio Results -->
    @foreach($results['portfolios'] as $portfolio)
    <div class="row mt-4">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">{{ $portfolio['name'] }}</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Position</th>
                                    <th>Candidate</th>
                                    <th>Votes</th>
                                    <th>Percentage</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($portfolio['candidates'] as $index => $candidate)
                                <tr class="{{ $candidate['is_winner'] ? 'table-success' : '' }}">
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <strong>{{ $candidate['name'] }}</strong>
                                    </td>
                                    <td>
                                        <strong>{{ $candidate['votes'] }}</strong>
                                    </td>
                                    <td>
                                        <div class="progress" style="height: 25px;">
                                            <div class="progress-bar {{ $candidate['is_winner'] ? 'bg-success' : 'bg-info' }}"
                                                 role="progressbar"
                                                 style="width: {{ $candidate['percentage'] }}%"
                                                 aria-valuenow="{{ $candidate['percentage'] }}"
                                                 aria-valuemin="0"
                                                 aria-valuemax="100">
                                                {{ $candidate['percentage'] }}%
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($candidate['is_winner'])
                                            <span class="badge badge-success">🏆 Winner</span>
                                        @else
                                            <span class="badge badge-secondary">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="table-dark">
                                    <td colspan="2"><strong>Total Votes</strong></td>
                                    <td colspan="3"><strong>{{ $portfolio['total_votes'] }}</strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach

</div>
<!-- ============================================================== -->
<!-- End Container fluid  -->
<!-- ============================================================== -->
@endsection
