@extends('layouts.app')

@section('title', 'Election Reports')

@section('content')
<!-- ============================================================== -->
<!-- Bread crumb and right sidebar toggle -->
<!-- ============================================================== -->
<div class="page-breadcrumb border-bottom">
    <div class="row">
        <div class="col-lg-3 col-md-4 col-xs-12 align-self-center">
            <h5 class="font-medium text-uppercase mb-0">Election Reports</h5>
        </div>
        <div class="col-lg-9 col-md-8 col-xs-12 align-self-center">
            <nav aria-label="breadcrumb" class="mt-2 float-md-right float-left">
                <ol class="breadcrumb mb-0 justify-content-end p-0">
                    <li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Reports</li>
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

                    <h4 class="card-title text-uppercase mb-0"> Election Reports
                    </h4><br>
                    <h5 class="card-subtitle">View election results and manage result approvals</h5>

                    <div class="table-responsive mt-4">
                        <table id="config-table" class="table display table-bordered table-striped no-wrap border">
                            <thead>
                                <tr>
                                    <th>Election Name</th>
                                    <th>Status</th>
                                    <th>Results Status</th>
                                    <th>Voters</th>
                                    <th>Candidates</th>
                                    <th>Portfolios</th>
                                    <th>Approval Admins</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($elections as $election)
                                @php
                                    $election->updateStatus();
                                @endphp
                                <tr>
                                    <td>
                                        <strong>{{ $election->name }}</strong>
                                        <br>
                                        <small class="text-muted">
                                            {{ $election->start_time?->format('M d, Y H:i') }} -
                                            {{ $election->end_time?->format('M d, Y H:i') }}
                                        </small>
                                    </td>
                                    <td>
                                        @switch($election->status)
                                            @case('draft')
                                                <span class="badge badge-secondary">Draft</span>
                                                @break
                                            @case('pending')
                                                <span class="badge badge-info">Pending</span>
                                                @break
                                            @case('in-progress')
                                                <span class="badge badge-warning">In Progress</span>
                                                @break
                                            @case('completed')
                                                <span class="badge badge-success">Completed</span>
                                                @break
                                            @default
                                                <span class="badge badge-secondary">{{ $election->status }}</span>
                                        @endswitch
                                    </td>
                                    <td>
                                        @switch($election->results_status)
                                            @case('pending')
                                                <span class="badge badge-secondary">Pending Release</span>
                                                @break
                                            @case('released')
                                                <span class="badge badge-warning">Awaiting Approval</span>
                                                @break
                                            @case('approved')
                                                <span class="badge badge-success">✓ Approved</span>
                                                @break
                                            @case('rejected')
                                                <span class="badge badge-danger">✗ Rejected</span>
                                                @break
                                            @default
                                                <span class="badge badge-secondary">{{ $election->results_status }}</span>
                                        @endswitch
                                        @if($election->results_released_at)
                                            <br>
                                            <small class="text-muted">
                                                Released: {{ $election->results_released_at->format('M d, Y H:i') }}
                                            </small>
                                        @endif
                                    </td>
                                    <td>{{ $election->voters_count }}</td>
                                    <td>{{ $election->candidates_count }}</td>
                                    <td>{{ $election->portfolios_count }}</td>
                                    <td>
                                        @if($election->approvalAdmins->count() > 0)
                                            <span class="badge badge-info">{{ $election->approvalAdmins->count() }} admin(s)</span>
                                        @else
                                            <span class="badge badge-danger">No admins</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('reports.view', $election->id) }}" class="btn btn-sm btn-info text-white mr-1">View</a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Election Name</th>
                                    <th>Status</th>
                                    <th>Results Status</th>
                                    <th>Voters</th>
                                    <th>Candidates</th>
                                    <th>Portfolios</th>
                                    <th>Approval Admins</th>
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
