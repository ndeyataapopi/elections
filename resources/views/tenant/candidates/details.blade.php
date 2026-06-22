@extends('layouts.app')

@section('title', 'Candidate Details')

@section('content')
<!-- ============================================================== -->
<!-- Bread crumb and right sidebar toggle -->
<!-- ============================================================== -->
<div class="page-breadcrumb border-bottom">
    <div class="row">
        <div class="col-lg-3 col-md-4 col-xs-12 align-self-center">
            <h5 class="font-medium text-uppercase mb-0">Candidate Details</h5>
        </div>
        <div class="col-lg-9 col-md-8 col-xs-12 align-self-center">
            <nav aria-label="breadcrumb" class="mt-2 float-md-right float-left">
                <ol class="breadcrumb mb-0 justify-content-end p-0">
                    <li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('candidates.index') }}">Candidates</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('candidates.view', $candidate->election_id) }}">{{ $candidate->election->name }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $candidate->first_name }} {{ $candidate->last_name }}</li>
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
                    
                    <h4 class="card-title text-uppercase mb-0"> Candidate Details - {{ $candidate->first_name }} {{ $candidate->last_name }}
                        <span class="pull-right">
                            <a class="btn btn-dark btn-sm" href="{{ route('candidates.view', $candidate->election_id) }}">
                            ← Back to Candidates </a>
                        </span>
                    </h4><br>
                    <h5 class="card-subtitle">View candidate information (read-only)</h5>

                    <div class="form-group mt-5 row">
                        <label for="example-text-input" class="col-2 col-form-label"><b>Election Details</b></label>
                    </div>

                    <div class="form-group row">
                        <label for="example-text-input" class="col-2 col-form-label">Election Name</label>
                        <div class="col-10">
                            <input class="form-control input" type="text" value="{{ $candidate->election->name }}" readonly>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="example-search-input" class="col-2 col-form-label">Start Date & Time</label>
                        <div class="col-10">
                            <input class="form-control input" type="datetime-local" value="{{ $candidate->election->start_time }}" readonly>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="example-search-input" class="col-2 col-form-label">End Date & Time</label>
                        <div class="col-10">
                            <input class="form-control input" type="datetime-local" value="{{ $candidate->election->end_time }}" readonly>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-3">
                            <!-- Photo Section -->
                            <div class="form-group row">
                                <div class="col-12 text-center">
                                    @if($candidate->photo)
                                        <img src="{{ Storage::url($candidate->photo) }}" alt="{{ $candidate->first_name }} {{ $candidate->last_name }}" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                                    @else
                                        <div class="img-thumbnail d-flex align-items-center justify-content-center" style="width: 200px; height: 200px; margin: 0 auto;">
                                            <i class="mdi mdi-account mdi-48px text-muted"></i>
                                        </div>
                                    @endif
                                    <p class="mt-2"><strong>Profile Photo</strong></p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-9">
                            <div class="form-group row">
                                <label for="example-text-input" class="col-3 col-form-label"><b>Personal Information</b></label>
                            </div>

                            <div class="form-group row">
                                <label for="example-text-input" class="col-3 col-form-label">Staff Number</label>
                                <div class="col-9">
                                    <input class="form-control input" type="text" value="{{ $candidate->staff_number ?? 'N/A' }}" readonly>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="example-text-input" class="col-3 col-form-label">First Name</label>
                                <div class="col-9">
                                    <input class="form-control input" type="text" value="{{ $candidate->first_name ?? 'N/A' }}" readonly>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="example-text-input" class="col-3 col-form-label">Last Name</label>
                                <div class="col-9">
                                    <input class="form-control input" type="text" value="{{ $candidate->last_name ?? 'N/A' }}" readonly>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="example-text-input" class="col-3 col-form-label">Gender</label>
                                <div class="col-9">
                                    <input class="form-control input" type="text" value="{{ $candidate->gender ?? 'N/A' }}" readonly>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="example-text-input" class="col-3 col-form-label">Email Address</label>
                                <div class="col-9">
                                    <input class="form-control input" type="email" value="{{ $candidate->email ?? 'N/A' }}" readonly>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="example-text-input" class="col-3 col-form-label">Cell Number</label>
                                <div class="col-9">
                                    <input class="form-control input" type="text" value="{{ $candidate->phone ?? 'N/A' }}" readonly>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="example-text-input" class="col-3 col-form-label">Job Title</label>
                                <div class="col-9">
                                    <input class="form-control input" type="text" value="{{ $candidate->job_title ?? 'N/A' }}" readonly>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="example-text-input" class="col-3 col-form-label">Portfolio</label>
                                <div class="col-9">
                                    <input class="form-control input" type="text" value="{{ $candidate->portfolio->name ?? 'N/A' }} (Max Votes: {{ $candidate->portfolio->max_votes ?? 'N/A' }})" readonly>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="example-text-input" class="col-3 col-form-label">Profile Status</label>
                                <div class="col-9">
                                    @if($candidate->profile_complete)
                                        <span class="badge badge-success">Completed</span>
                                    @else
                                        <span class="badge badge-warning">Incomplete</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="form-group row">
                        <label for="example-text-input" class="col-2 col-form-label"><b>Manifesto</b></label>
                    </div>

                    <div class="form-group row">
                        <div class="col-12">
                            <div class="form-control" style="min-height: 100px; background-color: #f8f9fa; border: 1px solid #ced4da;">
                                {{ $candidate->manifesto ?? 'No manifesto provided' }}
                            </div>
                        </div>
                    </div>

                    <div class="form-group col-10 mt-5 row">
                        <a href="{{ route('candidates.edit', $candidate->id) }}" class="btn btn-primary mr-2">Edit Candidate</a>
                        <a href="{{ route('candidates.notify.single', $candidate->id) }}" class="btn btn-warning mr-2 text-white">Notify Candidate</a>
                        <a href="{{ route('candidates.view', $candidate->election_id) }}" class="btn btn-dark">Back to List</a>
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
