@extends('layouts.app')

@section('title', 'Review Voters')

@section('content')
<!-- ============================================================== -->
<!-- Bread crumb and right sidebar toggle -->
<!-- ============================================================== -->
<div class="page-breadcrumb border-bottom">
    <div class="row">
        <div class="col-lg-3 col-md-4 col-xs-12 align-self-center">
            <h5 class="font-medium text-uppercase mb-0">Voters</h5>
        </div>
        <div class="col-lg-9 col-md-8 col-xs-12 align-self-center">
            <nav aria-label="breadcrumb" class="mt-2 float-md-right float-left">
                <ol class="breadcrumb mb-0 justify-content-end p-0">
                    <li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('voters.index') }}">Voters</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Review Voters</li>
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
                    
                    <h4 class="card-title text-uppercase mb-0"> Review Uploaded Voters - {{ $election->name }}
                        <span class="pull-right">
                            <a class="btn btn-dark btn-sm" href="{{ route('voters.index') }}">
                            ← Back to Voters </a>
                        </span>
                    </h4><br>
                    <h5 class="card-subtitle">Review and save uploaded voters</h5>

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

                    <!-- Review Form -->
                    <form id="voterReviewForm" method="POST" action="{{ route('voters.save', $election->id) }}">
                        @csrf
                        
                        <div class="alert alert-info">
                            <i class="mdi mdi-information"></i>
                            <strong>{{ count($voters) }} voters uploaded successfully!</strong> Please review before saving.
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
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($voters as $index => $voter)
                                    <tr>
                                        <td>{{ $voter['staff_number'] ?? 'N/A' }}</td>
                                        <td>{{ $voter['first_name'] ?? 'N/A' }}</td>
                                        <td>{{ $voter['last_name'] ?? 'N/A' }}</td>
                                        <td>{{ $voter['gender'] ?? 'N/A' }}</td>
                                        <td>{{ $voter['phone'] ?? 'N/A' }}</td>
                                        <td>{{ $voter['email'] ?? 'N/A' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="form-group col-10 mt-5 row">
                            <button type="submit" class="btn btn-success mr-2" id="saveBtn">
                                <i class="mdi mdi-content-save"></i> Save Voters
                            </button>
                            <a href="{{ route('voters.upload', $election->id) }}" class="btn btn-warning mr-2">
                                <i class="mdi mdi-upload"></i> Upload Different File
                            </a>
                            <a href="{{ route('voters.view', $election->id) }}" class="btn btn-dark">Cancel</a>
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
    $('#voterReviewForm').on('submit', function(e) {
        // Show loading state
        $('#saveBtn').prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin"></i> Saving...');
    });
});
</script>
@endpush
