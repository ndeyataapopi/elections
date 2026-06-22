@extends('layouts.app')

@section('title', 'Voter Details')

@section('content')
<!-- ============================================================== -->
<!-- Bread crumb and right sidebar toggle -->
<!-- ============================================================== -->
<div class="page-breadcrumb border-bottom">
    <div class="row">
        <div class="col-lg-3 col-md-4 col-xs-12 align-self-center">
            <h5 class="font-medium text-uppercase mb-0">Voter Details</h5>
        </div>
        <div class="col-lg-9 col-md-8 col-xs-12 align-self-center">
            <nav aria-label="breadcrumb" class="mt-2 float-md-right float-left">
                <ol class="breadcrumb mb-0 justify-content-end p-0">
                    <li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('voters.index') }}">Voters</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('voters.view', $voter->election_id) }}">{{ $voter->election->name }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $voter->first_name }} {{ $voter->last_name }}</li>
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
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <!-- <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button> -->
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <!-- <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button> -->
                        </div>
                    @endif

                    @if(session('warning'))
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            {{ session('warning') }}
                            <!-- <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button> -->
                        </div>
                    @endif
                    
                    <h4 class="card-title text-uppercase mb-0"> Voter Details - {{ $voter->first_name }} {{ $voter->last_name }}
                        <span class="pull-right">
                            <a class="btn btn-dark btn-sm" href="{{ route('voters.view', $voter->election_id) }}">
                            ← Back to Voters </a>
                        </span>
                    </h4><br>
                    <h5 class="card-subtitle">View voter information (read-only)</h5>

                    <div class="form-group mt-5 row">
                        <label for="example-text-input" class="col-2 col-form-label"><b>Election Details</b></label>
                    </div>

                    <div class="form-group row">
                        <label for="example-text-input" class="col-2 col-form-label">Election Name</label>
                        <div class="col-10">
                            <input class="form-control input" type="text" value="{{ $voter->election->name }}" readonly>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="example-search-input" class="col-2 col-form-label">Start Date & Time</label>
                        <div class="col-10">
                            <input class="form-control input" type="datetime-local" value="{{ $voter->election->start_time }}" readonly>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="example-search-input" class="col-2 col-form-label">End Date & Time</label>
                        <div class="col-10">
                            <input class="form-control input" type="datetime-local" value="{{ $voter->election->end_time }}" readonly>
                        </div>
                    </div>

                    <hr>

                    <div class="form-group row">
                        <label for="example-text-input" class="col-2 col-form-label"><b>Personal Information</b></label>
                    </div>

                    <div class="form-group row">
                        <label for="example-text-input" class="col-2 col-form-label">Staff Number</label>
                        <div class="col-10">
                            <input class="form-control input" type="text" value="{{ $voter->staff_number ?? 'N/A' }}" readonly>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="example-text-input" class="col-2 col-form-label">First Name</label>
                        <div class="col-10">
                            <input class="form-control input" type="text" value="{{ $voter->first_name ?? 'N/A' }}" readonly>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="example-text-input" class="col-2 col-form-label">Last Name</label>
                        <div class="col-10">
                            <input class="form-control input" type="text" value="{{ $voter->last_name ?? 'N/A' }}" readonly>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="example-text-input" class="col-2 col-form-label">Gender</label>
                        <div class="col-10">
                            <input class="form-control input" type="text" value="{{ $voter->gender ?? 'N/A' }}" readonly>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="example-text-input" class="col-2 col-form-label">Email Address</label>
                        <div class="col-10">
                            <input class="form-control input" type="email" value="{{ $voter->email ?? 'N/A' }}" readonly>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="example-text-input" class="col-2 col-form-label">Cell Number</label>
                        <div class="col-10">
                            <input class="form-control input" type="text" value="{{ $voter->phone ?? 'N/A' }}" readonly>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="example-text-input" class="col-2 col-form-label">Vote Status</label>
                        <div class="col-10">
                            @if($voter->has_voted)
                                <span class="badge badge-success">Voted</span>
                                <small class="form-text text-muted">Voted at: {{ $voter->voted_at }}</small>
                            @else
                                <span class="badge badge-warning">Not Voted</span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group col-10 mt-5 row">
                        <a href="{{ route('voters.edit', $voter->id) }}" class="btn btn-primary mr-2">Edit Voter</a>
                        <button class="btn btn-warning mr-2 text-white notify-voter-btn"
                                data-url="{{ route('voters.notify.single', $voter->id) }}"
                                data-name="{{ $voter->first_name }} {{ $voter->last_name }}">Notify Voter</button>
                        <a href="{{ route('voters.view', $voter->election_id) }}" class="btn btn-dark">Back to List</a>
                    </div>

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
    $('.notify-voter-btn').on('click', function(e) {
        e.preventDefault();
        var url = $(this).data('url');
        var voterName = $(this).data('name');

        if (!confirm('Send notification to ' + voterName + '?')) {
            return;
        }

        var $btn = $(this);
        var originalText = $btn.text();
        $btn.prop('disabled', true).text('Sending...');

        $.ajax({
            url: url,
            type: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            success: function(response) {
                $btn.prop('disabled', false).text(originalText);
                // Show success alert
                alert(response.message || 'Notification sent successfully!');
                window.location.reload();
            },
            error: function(xhr) {
                $btn.prop('disabled', false).text(originalText);
                // Parse and show error message
                var message = 'Failed to send notification';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                } else if (xhr.responseText) {
                    try {
                        var json = JSON.parse(xhr.responseText);
                        message = json.message || message;
                    } catch(e) {
                        message = xhr.responseText.substring(0, 200);
                    }
                }
                alert('Error: ' + message);
            }
        });
    });
});
</script>
@endpush
