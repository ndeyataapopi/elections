@extends('layouts.app')

@section('title', 'Edit Voter')

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
                    <li class="breadcrumb-item"><a href="{{ route('voters.view', $voter->election_id) }}">{{ $voter->election->name }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit Voter</li>
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

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

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
                    
                    <h4 class="card-title">Edit Voter - {{ $voter->first_name }} {{ $voter->last_name }}</h4>
                    <h5 class="card-subtitle">Update voter information</h5>
                    
                    <form data-toggle="validator" class="form" method="POST" action="{{ route('voters.update', $voter->id) }}">
                        @csrf

                        <div class="form-group mt-5 row">
                            <label for="example-text-input" class="col-2 col-form-label"><b>Voter Details</b></label>
                        </div>

                        <div class="form-group row">
                            <label for="example-text-input" class="col-2 col-form-label">Staff Number</label>
                            <div class="col-10">
                                <input class="form-control input" type="text" name="staff_number" value="{{ $voter->staff_number }}" placeholder="Staff Number" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="example-text-input" class="col-2 col-form-label">First Name</label>
                            <div class="col-10">
                                <input class="form-control input" type="text" name="first_name" value="{{ $voter->first_name }}" placeholder="First Name" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="example-text-input" class="col-2 col-form-label">Last Name</label>
                            <div class="col-10">
                                <input class="form-control input" type="text" name="last_name" value="{{ $voter->last_name }}" placeholder="Last Name" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="example-text-input" class="col-2 col-form-label">Gender</label>
                            <div class="col-10">
                                <select class="form-control" name="gender" required>
                                    <option value="">Select Gender</option>
                                    <option value="Male" {{ $voter->gender == 'Male' ? 'selected' : '' }}>Male</option>
                                    <option value="Female" {{ $voter->gender == 'Female' ? 'selected' : '' }}>Female</option>
                                    <option value="Other" {{ $voter->gender == 'Other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="example-text-input" class="col-2 col-form-label">Email Address</label>
                            <div class="col-10">
                                <input class="form-control input" type="email" name="email" value="{{ $voter->email }}" placeholder="Email Address">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="example-text-input" class="col-2 col-form-label">Cell Number</label>
                            <div class="col-10">
                                <input class="form-control input" type="text" name="phone" value="{{ $voter->phone }}" placeholder="Cell Number">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="example-text-input" class="col-2 col-form-label">Vote Status</label>
                            <div class="col-10">
                                <div class="form-control-plaintext">
                                    @if($voter->has_voted)
                                        <span class="badge badge-success">Voted</span>
                                        <small class="form-text text-muted">Voted at: {{ $voter->voted_at }}</small>
                                    @else
                                        <span class="badge badge-warning">Not Voted</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="form-group col-10 mt-5 row">
                            <button type="submit" class="btn btn-success mr-2">Update Voter</button>
                            <a href="{{ route('voters.view', $voter->election_id) }}" class="btn btn-dark">Cancel</a>
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
    
</script>
@endpush