@extends('layouts.app')

@section('title', 'Election Voters')

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
                    
                    <h4 class="card-title text-uppercase mb-0"> Voters for {{ $election->name }}
                        <span class="pull-right">
                            <a class="btn btn-dark btn-sm" href="{{ route('voters.index') }}">
                            ← Back to Voters </a>
                        </span>
                    </h4><br>
                    <h5 class="card-subtitle">Manage Voters for this Election</h5>

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
                                    <th>Vote Status</th>
                                    <th>Action</th>
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
                                        @if($voter->has_voted ?? false)
                                            <span class="badge badge-success">Voted</span>
                                        @else
                                            <span class="badge badge-warning">Not Voted</span>
                                        @endif
                                    </td>
                                    <td> 
                                        <a href="{{ route('voters.details', $voter->id) }}" class="btn btn-sm btn-info text-white mr-1">View</a>
                                        <a href="{{ route('voters.edit', $voter->id) }}" class="btn btn-sm btn-primary text-white mr-1">Edit</a>
                                        <form action="{{ route('voters.delete', $voter->id) }}" method="POST" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger text-white" onclick="return confirm('Are you sure you want to delete this voter?')">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Staff Number</th>
                                    <th>Firstnames</th>
                                    <th>Surname</th>
                                    <th>Gender</th>
                                    <th>Cell Number</th>
                                    <th>Email</th>
                                    <th>Vote Status</th>
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
