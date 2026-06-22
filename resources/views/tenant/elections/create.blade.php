@extends('layouts.app')

@section('title', 'Create Election')

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
                    <h4 class="card-title">Create New Election</h4>
                    <h5 class="card-subtitle">Complete all required fields below</h5>
                    
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form id="elections-form" data-toggle="validator" enctype="multipart/form-data" class="form" method="POST" action="{{ route('elections.store') }}">
                        @csrf

                        <div class="form-group mt-5 row">
                            <label for="example-text-input" class="col-2 col-form-label"><b>Election Details</b></label>
                        </div>

                        <div class="form-group row">
                            <label for="example-text-input" class="col-2 col-form-label">Election Name</label>
                            <div class="col-10">
                                <input class="form-control input" type="text" name="election_name" placeholder="Election Name" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="example-search-input" class="col-2 col-form-label">Start Date & Time</label>
                            <div class="col-10">
                                <input class="form-control input" type="datetime-local" name="start_time" placeholder="Select Start Date & Time" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="example-search-input" class="col-2 col-form-label">End Date & Time</label>
                            <div class="col-10">
                                <input class="form-control input" type="datetime-local" name="end_time" placeholder="Select End Date & Time" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="example-text-input" class="col-2 col-form-label">Votes Per Voter</label>
                            <div class="col-10">
                                <input class="form-control input" type="number" name="allowed_votes" placeholder="Enter Allowed Number of Votes Per Voter" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="example-text-input" class="col-2 col-form-label">Admins For Approval</label>
                            <div class="col-10">
                                <select class="select2 form-control" multiple="multiple" name="approval_admins[]" style="height: 36px;width: 100%;" required>
                                    @foreach($clientAdmins as $admin)
                                        <option value="{{ $admin->id }}">{{ $admin->name }} ({{ $admin->email }})</option>
                                    @endforeach
                                </select>
                                <small>Select Client Admin users who can approve election results</small>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="example-text-input" class="col-2 col-form-label">Terms & Conditions</label>
                            <div class="col-10">
                                <!-- Create the editor container -->
                                <div id="editor" style="height: 200px;">
                                    <p>Welcome to Elections!</p>
                                    <p>Type or copy and paste your election <strong>Terms & Conditions</strong> here...</p>
                                    <p>
                                        <br>
                                    </p>
                                </div>
                                <!-- Hidden input field to store HTML content -->
                                <input type="hidden" name="content" id="quill_html_content">
                            </div>
                        </div>

                        <hr>

                        <div class="form-group row">
                            <label for="example-text-input" class="col-2 col-form-label"><b>Portfolio Details</b></label>
                        </div>

                        <div class="email-repeater form-group">
                            <div data-repeater-list="portfolio_name">
                                <div data-repeater-item class="row">
                                    <div class="col-2 col-form-label">
                                        <label for="example-name-input" class="col-form-label">Portfolio Name</label>
                                    </div>
                                    <div class="col-8">
                                        <input class="form-control" type="text" name="portfolio" placeholder="Enter Election Portfolio Name" required id="example-name-input">
                                    </div>
                                    <div class="col-2">
                                        <button data-repeater-delete="" class="btn btn-danger waves-effect waves-light" type="button">
                                            <i class="ti-close"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <button type="button" data-repeater-create="" class="btn btn-info waves-effect waves-light">+ Portfolio</button>
                        </div>


                        <div class="form-group col-10 mt-5 row">
                            <button type="submit" class="btn btn-success mr-2">Save</button>
                            <a class="btn btn-dark" href="{{ route('elections.index') }}">Cancel</a>
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

@endpush