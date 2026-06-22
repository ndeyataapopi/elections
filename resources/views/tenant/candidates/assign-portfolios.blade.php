@extends('layouts.app')

@section('title', 'Assign Portfolios to Candidates')

@section('content')
<!-- ============================================================== -->
<!-- Bread crumb and right sidebar toggle -->
<!-- ============================================================== -->
<div class="page-breadcrumb border-bottom">
    <div class="row">
        <div class="col-lg-3 col-md-4 col-xs-12 align-self-center">
            <h5 class="font-medium text-uppercase mb-0">Candidates</h5>
        </div>
        <div class="col-lg-9 col-md-8 col-xs-12 align-self-center">
            <nav aria-label="breadcrumb" class="mt-2 float-md-right float-left">
                <ol class="breadcrumb mb-0 justify-content-end p-0">
                    <li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('candidates.index') }}">Candidates</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Assign Portfolios</li>
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
    <!-- ============================================================== -->
    <!-- First Cards Row  -->
    <!-- ============================================================== -->
    
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">

                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif
                    
                    <h4 class="card-title text-uppercase mb-0"> Assign Portfolios to Candidates - {{ $election->name }}
                        <span class="pull-right">
                            <a class="btn btn-dark btn-sm" href="{{ route('candidates.index') }}">
                            ← Back to Candidates </a>
                        </span>
                    </h4><br>
                    <h5 class="card-subtitle">Assign portfolios to uploaded candidates below</h5>

                    <div class="form-group mt-5 row">
                        <label for="example-text-input" class="col-2 col-form-label"><b>Election Details</b></label>
                    </div>

                    <div class="form-group row">
                        <label for="example-text-input" class="col-2 col-form-label">Election Name</label>
                        <div class="col-10">
                            <input class="form-control input" type="text" value="{{ $election->name }}" readonly>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="example-search-input" class="col-2 col-form-label">Start Date & Time</label>
                        <div class="col-10">
                            <input class="form-control input" type="datetime-local" value="{{ $election->start_time }}" readonly>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="example-search-input" class="col-2 col-form-label">End Date & Time</label>
                        <div class="col-10">
                            <input class="form-control input" type="datetime-local" value="{{ $election->end_time }}" readonly>
                        </div>
                    </div>

                    <hr>

                    <!-- Portfolio Assignment Form -->
                    <form id="portfolioAssignmentForm" method="POST" action="{{ route('candidates.save.portfolios', $election->id) }}">
                        @csrf
                        
                        <div class="alert alert-info">
                            <i class="mdi mdi-information"></i>
                            <strong>{{ count($candidates) }} candidates uploaded successfully!</strong> Please assign a portfolio to each candidate below.
                        </div>

                        <div class="table-responsive">
                            <table class="table display table-bordered table-striped no-wrap border">
                                <thead>
                                    <tr>
                                        <th>Staff Number</th>
                                        <th>Firstnames</th>
                                        <th>Surname</th>
                                        <th>Gender</th>
                                        <th>Cell Number</th>
                                        <th>Email</th>
                                        <th>Portfolio Assignment</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($candidates as $index => $candidate)
                                    <tr>
                                        <td>{{ $candidate['staff_number'] ?? 'N/A' }}</td>
                                        <td>{{ $candidate['first_name'] ?? 'N/A' }}</td>
                                        <td>{{ $candidate['last_name'] ?? 'N/A' }}</td>
                                        <td>{{ $candidate['gender'] ?? 'N/A' }}</td>
                                        <td>{{ $candidate['phone'] ?? 'N/A' }}</td>
                                        <td>{{ $candidate['email'] ?? 'N/A' }}</td>
                                        <td>
                                            <select name="candidates[{{ $index }}][portfolio_id]" class="form-control" required>
                                                <option value="">Select Portfolio</option>
                                                @foreach($election->portfolios as $portfolio)
                                                    <option value="{{ $portfolio->id }}">{{ $portfolio->name }} (Max Votes: {{ $portfolio->max_votes }})</option>
                                                @endforeach
                                            </select>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="form-group col-10 mt-5 row">
                            <button type="submit" class="btn btn-success mr-2" id="saveBtn">
                                <i class="mdi mdi-content-save"></i> Save Candidates & Assign Portfolios
                            </button>
                            <a href="{{ route('candidates.upload', $election->id) }}" class="btn btn-warning mr-2">
                                <i class="mdi mdi-upload"></i> Upload Different File
                            </a>
                            <a href="{{ route('candidates.view', $election->id) }}" class="btn btn-dark">Cancel</a>
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

@push('scripts')
<script>
$(document).ready(function() {
    $('#portfolioAssignmentForm').on('submit', function(e) {
        // Validate all portfolio selections
        let isValid = true;
        $('select[name*="portfolio_id"]').each(function() {
            if (!$(this).val()) {
                isValid = false;
                $(this).addClass('is-invalid');
            } else {
                $(this).removeClass('is-invalid');
            }
        });

        if (!isValid) {
            e.preventDefault();
            alert('Please assign a portfolio to all candidates before saving.');
            return false;
        }

        // Show loading state
        $('#saveBtn').prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin"></i> Saving...');
    });
});
</script>
@endpush
