@extends('layouts.app')

@section('title', 'Voters')

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
                    <li class="breadcrumb-item active" aria-current="page">Voters</li>
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

                    @if(session('warning'))
                        <div class="alert alert-warning">
                            {{ session('warning') }}
                        </div>
                    @endif

                    <h4 class="card-title text-uppercase mb-0"> Voters
                    </h4><br>
                    <h5 class="card-subtitle">All Elections and Voter Information</h5>

                    <div class="table-responsive">
                        <table id="config-table" class="table display table-bordered table-striped no-wrap border">
                            <thead>
                                <tr>
                                    <th>Election Name</th>
                                    <th>Portfolio Count</th>
                                    <th>Candidates Upload</th>
                                    <th>Voters Upload</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($elections as $election)
                                <tr>
                                    <td>{{ $election->name }}</td>
                                    <td>{{ $election->portfolios_count }} Portfolio(s)</td>
                                    <td>
                                        @if($election->candidates_count > 0)
                                            <span class="badge badge-success">{{ $election->candidates_count }} Uploaded</span>
                                        @else
                                            <span class="badge badge-secondary">Not Uploaded</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($election->voters_count > 0)
                                            <span class="badge badge-success">{{ $election->voters_count }} Uploaded</span>
                                        @else
                                            <span class="badge badge-secondary">Not Uploaded</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('voters.view', $election->id) }}" class="btn btn-sm btn-info text-white mr-1">View</a>
                                        <a href="{{ route('voters.upload', $election->id) }}" class="btn btn-sm btn-primary text-white mr-1">Upload</a>
                                        <button class="btn btn-sm btn-warning text-white notify-btn" data-url="{{ route('voters.notify', $election->id) }}" data-election="{{ $election->name }}">Notify</button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Election Name</th>
                                    <th>Portfolio Count</th>
                                    <th>Candidates Upload</th>
                                    <th>Voters Upload</th>
                                    <th>Action</th>
                                </tr>
                            </tfoot>
                        </table>
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

<!-- Notification Progress Modal -->
<div class="modal fade" id="notifyModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Sending Notifications</h5>
            </div>
            <div class="modal-body">
                <p id="notifyMessage">Processing notifications for <span id="electionName"></span>...</p>
                <div class="progress">
                    <div id="notifyProgressBar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%">
                        0%
                    </div>
                </div>
                <p id="notifyStatus" class="mt-2 text-muted">Initializing...</p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    $('.notify-btn').on('click', function(e) {
        e.preventDefault();
        var url = $(this).data('url');
        var electionName = $(this).data('election');

        $('#electionName').text(electionName);
        $('#notifyModal').modal('show');
        $('#notifyStatus').text('Sending notifications...');

        // Simulate progress while waiting
        var progress = 0;
        var progressBar = $('#notifyProgressBar');
        var interval = setInterval(function() {
            progress += 5;
            if (progress > 90) progress = 90;
            progressBar.css('width', progress + '%').text(progress + '%');
        }, 500);

        // Send AJAX request
        $.ajax({
            url: url,
            type: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            success: function(response) {
                clearInterval(interval);
                progressBar.css('width', '100%').text('100%');
                $('#notifyStatus').text('Completed!');

                setTimeout(function() {
                    $('#notifyModal').modal('hide');
                    // Show success alert
                    alert(response.message || 'Notifications sent successfully!');
                    window.location.reload();
                }, 1500);
            },
            error: function(xhr) {
                clearInterval(interval);
                $('#notifyStatus').text('Error occurred!');
                progressBar.removeClass('progress-bar-animated').addClass('bg-danger');

                setTimeout(function() {
                    $('#notifyModal').modal('hide');
                    // Parse and show error message
                    var message = 'Failed to send notifications';
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
                }, 1500);
            }
        });
    });
});
</script>
@endpush