@extends('layouts.app')

@section('title', 'Elections')

@section('content')
<!-- ============================================================== -->
<!-- Bread crumb and right sidebar toggle -->
<!-- ============================================================== -->
<div class="page-breadcrumb border-bottom">
    <div class="row">
        <div class="col-lg-3 col-md-4 col-xs-12 align-self-center">
            <h5 class="font-medium text-uppercase mb-0">Elections</h5>
        </div>
        <div class="col-lg-9 col-md-8 col-xs-12 align-self-center">
            <!-- <button class="btn btn-danger text-white float-right ml-3 d-none d-md-block">Buy Ample Admin</button> -->
            <nav aria-label="breadcrumb" class="mt-2 float-md-right float-left">
                <ol class="breadcrumb mb-0 justify-content-end p-0">
                    <li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Elections</li>
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
                    
                    <h4 class="card-title text-uppercase mb-0"> Elections
                        <span class="pull-right">
                            <a class="btn btn-outline-info btn-sm" href="{{ route('elections.create') }}">
                            + New Election </a>
                        </span>
                    </h4><br>
                    <h5 class="card-subtitle">All Elections and their Status</h5>

                    <div class="table-responsive">
                        <!-- <table id="zero_config" class="table table-striped border"> -->
                        <table id="config-table" class="table display table-bordered table-striped no-wrap border">
                            <thead>
                                <tr>
                                    <th>Election Name</th>
                                    <th>Start Date & Time</th>
                                    <th>End Date & Time</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($elections as $election)
                                <tr>
                                    <td>{{ $election->name }}</td>
                                    <td>{{ $election->start_time }}</td>
                                    <td>{{ $election->end_time }}</td>
                                    <td>
                                        @if($election->status === 'draft')
                                            <span class="badge badge-secondary">Draft</span>
                                        @elseif(now()->gt($election->end_time))
                                            <span class="badge badge-danger">Completed</span>
                                        @elseif(now()->between($election->start_time, $election->end_time))
                                            <span class="badge badge-success">In Progress</span>
                                        @elseif(now()->lt($election->start_time))
                                            <span class="badge badge-info">Pending</span>
                                        @else
                                            <span class="badge badge-warning">{{ ucfirst($election->status) }}</span>
                                        @endif
                                    </td>
                                    <td> 
                                        <a href="{{ route('elections.edit', $election->id) }}" class="btn btn-sm btn-primary text-white mr-1">Edit</a>

                                        @if($election->status === 'draft' || now()->lt($election->start_time))
                                            <form action="{{ route('elections.destroy', $election->id) }}" method="POST" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger text-white" onclick="return confirm('Are you sure you want to delete this election? This action cannot be undone.')">Delete</button>
                                            </form>
                                        @endif
                                        
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Election Name</th>
                                    <th>Start Date & Time</th>
                                    <th>End Date & Time</th>
                                    <th>Status</th>
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

@push('scripts')
<script>
    
</script>
@endpush