@extends('layouts.app')

@section('title', 'Approve Election Results - ' . $election->name)

@section('content')
<!-- ============================================================== -->
<!-- Bread crumb and right sidebar toggle -->
<!-- ============================================================== -->
<div class="page-breadcrumb border-bottom">
    <div class="row">
        <div class="col-lg-3 col-md-4 col-xs-12 align-self-center">
            <h5 class="font-medium text-uppercase mb-0">Approve Results</h5>
        </div>
        <div class="col-lg-9 col-md-8 col-xs-12 align-self-center">
            <nav aria-label="breadcrumb" class="mt-2 float-md-right float-left">
                <ol class="breadcrumb mb-0 justify-content-end p-0">
                    <li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('reports.index') }}">Reports</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('reports.view', $election->id) }}">{{ $election->name }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Approve</li>
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

    <!-- Approval Notice -->
    <div class="row">
        <div class="col-sm-12">
            <div class="alert alert-info">
                <h5><i class="fas fa-info-circle"></i> Election Approval Administrator Duty</h5>
                <p class="mb-0">You have been designated as an Election Approval Administrator for <strong>{{ $election->name }}</strong>.
                Please review the election results below carefully and make your determination on whether this election was conducted in a <strong>free and fair</strong> manner.</p>
            </div>
        </div>
    </div>

    <!-- Election Info Card -->
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title text-uppercase mb-0">
                        {{ $election->name }}
                        <span class="pull-right">
                            <a class="btn btn-dark btn-sm" href="{{ route('reports.view', $election->id) }}">
                            ← Back to Results </a>
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

                    <!-- Results Info -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="alert alert-warning">
                                <h5>Results Released</h5>
                                <p class="mb-1"><strong>Released at:</strong> {{ $election->results_released_at?->format('F d, Y H:i') }}</p>
                                @if($election->resultsReleasedBy)
                                    <p class="mb-0"><strong>Released by:</strong> {{ $election->resultsReleasedBy->name ?? 'Unknown' }}</p>
                                @endif
                            </div>
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

    <!-- Approval Form -->
    <div class="row mt-4">
        <div class="col-sm-12">
            <div class="card border-success">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-gavel"></i> Your Approval Decision</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('reports.approve', $election->id) }}" method="POST">
                        @csrf

                        <div class="form-group">
                            <label for="decision" class="font-weight-bold">Your Decision</label>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="decision" id="decision_approve" value="approve" required>
                                        <label class="form-check-label text-success font-weight-bold" for="decision_approve">
                                            <i class="fas fa-check-circle"></i> APPROVE - Election was FREE and FAIR
                                        </label>
                                    </div>
                                    <small class="form-text text-muted ml-4">
                                        Approve these results and certify the election as conducted in a free and fair manner.
                                    </small>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="decision" id="decision_reject" value="reject">
                                        <label class="form-check-label text-danger font-weight-bold" for="decision_reject">
                                            <i class="fas fa-times-circle"></i> REJECT - Issues Identified
                                        </label>
                                    </div>
                                    <small class="form-text text-muted ml-4">
                                        Reject these results if you identified irregularities or issues.
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mt-4">
                            <label for="notes">Notes / Comments</label>
                            <textarea class="form-control" id="notes" name="notes" rows="4" placeholder="Enter any observations, concerns, or confirmation notes..."></textarea>
                            <small class="form-text text-muted">
                                If rejecting, please specify the issues or irregularities observed. If approving, you may leave a confirmation note.
                            </small>
                        </div>

                        <div class="alert alert-warning mt-4">
                            <strong><i class="fas fa-exclamation-triangle"></i> Important:</strong>
                            <p class="mb-0">By submitting this form, you are making an official determination on the validity of this election. Your decision will be recorded and the election will be certified accordingly.</p>
                        </div>

                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-success btn-lg mr-2" onclick="return confirm('Are you sure you want to submit this decision? This action cannot be undone.');">
                                <i class="fas fa-check"></i> Submit Decision
                            </button>
                            <a href="{{ route('reports.view', $election->id) }}" class="btn btn-secondary btn-lg">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
<!-- ============================================================== -->
<!-- End Container fluid  -->
<!-- ============================================================== -->
@endsection
