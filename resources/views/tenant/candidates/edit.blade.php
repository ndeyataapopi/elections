@extends('layouts.app')

@section('title', 'Edit Candidate')

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
                    <li class="breadcrumb-item"><a href="{{ route('candidates.view', $candidate->election_id) }}">{{ $candidate->election->name }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit Candidate</li>
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
                    
                    <h4 class="card-title">Edit Candidate - {{ $candidate->first_name }} {{ $candidate->last_name }}</h4>
                    <h5 class="card-subtitle">Update candidate information</h5>
                    
                    <form data-toggle="validator" enctype="multipart/form-data" class="form" method="POST" action="{{ route('candidates.update', $candidate->id) }}">
                        @csrf

                        <div class="form-group mt-5 row">
                            <label for="example-text-input" class="col-2 col-form-label"><b>Candidate Details</b></label>
                        </div>

                        <div class="form-group row">
                            <label for="example-text-input" class="col-2 col-form-label">Staff Number</label>
                            <div class="col-10">
                                <input class="form-control input" type="text" name="staff_number" value="{{ $candidate->staff_number }}" placeholder="Staff Number" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="example-text-input" class="col-2 col-form-label">First Name</label>
                            <div class="col-10">
                                <input class="form-control input" type="text" name="first_name" value="{{ $candidate->first_name }}" placeholder="First Name" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="example-text-input" class="col-2 col-form-label">Last Name</label>
                            <div class="col-10">
                                <input class="form-control input" type="text" name="last_name" value="{{ $candidate->last_name }}" placeholder="Last Name" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="example-text-input" class="col-2 col-form-label">Gender</label>
                            <div class="col-10">
                                <select class="form-control" name="gender" required>
                                    <option value="">Select Gender</option>
                                    <option value="Male" {{ $candidate->gender == 'Male' ? 'selected' : '' }}>Male</option>
                                    <option value="Female" {{ $candidate->gender == 'Female' ? 'selected' : '' }}>Female</option>
                                    <option value="Other" {{ $candidate->gender == 'Other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="example-text-input" class="col-2 col-form-label">Email Address</label>
                            <div class="col-10">
                                <input class="form-control input" type="email" name="email" value="{{ $candidate->email }}" placeholder="Email Address">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="example-text-input" class="col-2 col-form-label">Cell Number</label>
                            <div class="col-10">
                                <input class="form-control input" type="text" name="phone" value="{{ $candidate->phone }}" placeholder="Cell Number">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="example-text-input" class="col-2 col-form-label">Job Title</label>
                            <div class="col-10">
                                <input class="form-control input" type="text" name="job_title" value="{{ $candidate->job_title }}" placeholder="Job Title">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="example-text-input" class="col-2 col-form-label">Manifesto</label>
                            <div class="col-10">
                                <textarea class="form-control" name="manifesto" rows="4" placeholder="Candidate manifesto or statement">{{ $candidate->manifesto }}</textarea>
                                <small class="form-text text-muted">Maximum 2000 characters</small>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="example-text-input" class="col-2 col-form-label">Photo</label>
                            <div class="col-10">
                                @if($candidate->photo)
                                    <div class="mb-2">
                                        <img src="{{ Storage::url($candidate->photo) }}" alt="{{ $candidate->first_name }} {{ $candidate->last_name }}" class="img-thumbnail" style="max-width: 150px; max-height: 150px;">
                                        <br>
                                        <small class="form-text text-muted">Current photo</small>
                                    </div>
                                @endif
                                <input type="file" name="photo" class="form-control" accept="image/*">
                                <small class="form-text text-muted">Upload new photo (JPEG, PNG, JPG, GIF - Max 2MB). Leave empty to keep current photo.</small>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="example-text-input" class="col-2 col-form-label">Portfolio</label>
                            <div class="col-10">
                                <select class="form-control" name="portfolio_id" required>
                                    <option value="">Select Portfolio</option>
                                    @foreach($candidate->election->portfolios as $portfolio)
                                        <option value="{{ $portfolio->id }}" {{ $candidate->portfolio_id == $portfolio->id ? 'selected' : '' }}>
                                            {{ $portfolio->name }} (Max Votes: {{ $portfolio->max_votes }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="example-text-input" class="col-2 col-form-label">Profile Status</label>
                            <div class="col-10">
                                <div class="form-control-plaintext">
                                    @if($candidate->profile_complete)
                                        <span class="badge badge-success">Completed</span>
                                    @else
                                        <span class="badge badge-warning">Incomplete</span>
                                    @endif
                                    <small class="form-text text-muted">Profile completion status (managed by candidate)</small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group col-10 mt-5 row">
                            <button type="submit" class="btn btn-success mr-2">Update Candidate</button>
                            <a href="{{ route('candidates.view', $candidate->election_id) }}" class="btn btn-dark">Cancel</a>
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