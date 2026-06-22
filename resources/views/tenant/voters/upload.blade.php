@extends('layouts.app')

@section('title', 'Upload Voters')

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
                    <li class="breadcrumb-item active" aria-current="page">Upload Voters</li>
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
                    
                    <h4 class="card-title text-uppercase mb-0"> Upload Voters for {{ $election->name }}
                        <span class="pull-right">
                            <a class="btn btn-dark btn-sm" href="{{ route('voters.index') }}">
                            ← Back to Voters </a>
                        </span>
                    </h4><br>
                    <h5 class="card-subtitle">Upload Voter Information via Excel File</h5>

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

                    <!-- Upload Form -->
                    <form id="uploadForm" enctype="multipart/form-data" method="POST" action="{{ route('voters.upload.file', $election->id) }}">
                        @csrf
                        <div class="form-group row">
                            <label for="excel-file" class="col-2 col-form-label">Excel File</label>
                            <div class="col-6">
                                <input type="file" id="excel-file" name="excel_file" class="form-control" accept=".xlsx,.xls" required>
                                <small class="form-text text-muted">Upload voter data in Excel format (.xlsx or .xls)</small>
                            </div>
                            <div class="col-4">
                                <a href="/templates/voters-template.xlsx" class="btn btn-outline-info btn-sm" download>
                                    <i class="mdi mdi-download"></i> Download Template
                                </a>
                                <small class="form-text text-muted d-block">Download the Excel template</small>
                            </div>
                        </div>

                        <!-- Progress Bar (initially hidden) -->
                        <div id="progressContainer" class="form-group row" style="display: none;">
                            <label class="col-2 col-form-label">Progress</label>
                            <div class="col-10">
                                <div class="progress">
                                    <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%">
                                        0%
                                    </div>
                                </div>
                                <small id="progressText" class="form-text text-muted">Uploading...</small>
                            </div>
                        </div>

                        <div class="form-group col-10 mt-5 row">
                            <button type="submit" class="btn btn-success mr-2" id="uploadBtn">
                                <i class="mdi mdi-upload"></i> Upload Voters
                            </button>
                            <a href="{{ route('voters.view', $election->id) }}" class="btn btn-dark">Cancel</a>
                        </div>
                    </form>

                    <!-- Voters Table (if any exist) -->
                    @if($election->voters->count() > 0)
                    <hr>
                    <h5 class="card-subtitle">Current Candidates ({{ $election->voters->count() }})</h5>
                    <div class="table-responsive">
                        <table id="config-table" class="table display table-bordered table-striped no-wrap border">
                            <thead>
                                <tr>
                                    <th>Staff Number</th>
                                    <th>Firstnames</th>
                                    <th>Surname</th>
                                    <th>Gender</th>
                                    <th>Cell Number</th>
                                    <th>Email</th>
                                    <th>Profile Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($election->voters as $voter)
                                <tr>
                                    <td>{{ $voter->staff_number ?? 'N/A' }}</td>
                                    <td>{{ $voter->first_name ?? 'N/A' }}</td>
                                    <td>{{ $voter->last_name ?? 'N/A' }}</td>
                                    <td>{{ $voter->gender ?? 'N/A' }}</td>
                                    <td>{{ $voter->phone ?? 'N/A' }}</td>
                                    <td>{{ $voter->email ?? 'N/A' }}</td>
                                    <td>
                                        @if($voter->profile_complete ?? false)
                                            <span class="badge badge-success">Completed</span>
                                        @else
                                            <span class="badge badge-warning">Incomplete</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif

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
    $('#uploadForm').on('submit', function(e) {
        e.preventDefault();

        var form = this;
        var formData = new FormData(form);
        var actionUrl = $(form).attr('action');
        var progressBar = $('#progressBar');
        var progressContainer = $('#progressContainer');
        var progressText = $('#progressText');
        var uploadBtn = $('#uploadBtn');

        // Show progress bar and disable button
        progressContainer.show();
        uploadBtn.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin"></i> Uploading...');

        // Simulate progress (in real implementation, this would be actual upload progress)
        var progress = 0;
        var interval = setInterval(function() {
            progress += 10;
            progressBar.css('width', progress + '%').text(progress + '%');

            if (progress >= 100) {
                clearInterval(interval);
                progressText.text('Processing file...');

                // Submit the form
                $.ajax({
                    url: actionUrl,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        progressText.text('Upload completed! Redirecting...');
                        setTimeout(function() {
                            window.location.href = '{{ route("voters.review", $election->id) }}';
                        }, 1500);
                    },
                    error: function(xhr) {
                        progressText.text('Upload failed!');
                        uploadBtn.prop('disabled', false).html('<i class="mdi mdi-upload"></i> Upload Voters');
                        alert('Error: ' + xhr.responseText);
                    }
                });
            }
        }, 200);
    });
});
</script>
@endpush
