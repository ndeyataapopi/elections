@extends('layouts.app')

@section('title', 'Create Tenant')

@section('content')
<!-- ============================================================== -->
<!-- Bread crumb and right sidebar toggle -->
<!-- ============================================================== -->
<div class="page-breadcrumb border-bottom">
    <div class="row">
        <div class="col-lg-3 col-md-4 col-xs-12 align-self-center">
            <h5 class="font-medium text-uppercase mb-0">Tenants</h5>
        </div>
        <div class="col-lg-9 col-md-8 col-xs-12 align-self-center">
            <!-- <button class="btn btn-danger text-white float-right ml-3 d-none d-md-block">Buy Ample Admin</button> -->
            <nav aria-label="breadcrumb" class="mt-2 float-md-right float-left">
                <ol class="breadcrumb mb-0 justify-content-end p-0">
                    <li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Tenants</li>
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
                    <h4 class="card-title">Create New Tenant</h4>
                    <h5 class="card-subtitle">Complete All Required fields below</h5>
                    
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form data-toggle="validator" enctype="multipart/form-data" class="form" method="POST" action="{{ route('tenants.store') }}">
                        @csrf

                        <div class="form-group mt-5 row">
                            <label for="example-text-input" class="col-2 col-form-label"><b>Tenant Details</b></label>
                        </div>

                        <div class="form-group row">
                            <label for="example-text-input" class="col-2 col-form-label">Tenant Name</label>
                            <div class="col-10">
                                <input class="form-control input" type="text" name="name" placeholder="Tenant Name" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="example-search-input" class="col-2 col-form-label">Subdomain</label>
                            <div class="col-10">
                                <input class="form-control input" type="text" name="subdomain" placeholder="Subdomain (e.g elections)" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="example-search-input" class="col-2 col-form-label">Logo</label>
                            <div class="col-10">
                                <input class="form-control input" type="file" name="image" required>
                            </div>
                        </div>

                        <hr>

                        <div class="form-group row">
                            <label for="example-text-input" class="col-2 col-form-label"><b>Tenant Admin Details</b></label>
                        </div>

                        <div class="form-group row">
                            <label for="example-name-input" class="col-2 col-form-label">Admin Name</label>
                            <div class="col-10">
                                <input class="form-control" type="text" name="admin_name" placeholder="Admin Name" required id="example-name-input">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="example-email-input" class="col-2 col-form-label">Admin Email</label>
                            <div class="col-10">
                                <input class="form-control" type="email" name="admin_email" placeholder="Admin Email" required id="example-email-input">
                            </div>
                        </div>

                        <!-- <div class="form-group row">
                            <label for="example-tel-input" class="col-2 col-form-label">Admin Password</label>
                            <div class="col-10">
                                <input class="form-control" type="tel" value="1-(555)-555-5555" id="example-tel-input">
                            </div>
                        </div> -->

                        <div class="form-group row">
                            <label for="example-password-input" class="col-2 col-form-label">Password</label>
                            <div class="col-10">
                                <input class="form-control" type="password" name="admin_password" Placeholder="Password" id="example-password-input">
                            </div>
                        </div>

                        <!-- <div class="form-group row">
                            <label for="example-number-input" class="col-2 col-form-label">Number</label>
                            <div class="col-10">
                                <input class="form-control" type="number" value="42" id="example-number-input">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="example-datetime-local-input" class="col-2 col-form-label">Date and time</label>
                            <div class="col-10">
                                <input class="form-control" type="datetime-local" value="2011-08-19T13:45:00" id="example-datetime-local-input">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="example-date-input" class="col-2 col-form-label">Date</label>
                            <div class="col-10">
                                <input class="form-control" type="date" value="2011-08-19" id="example-date-input">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="example-month-input" class="col-2 col-form-label">Month</label>
                            <div class="col-10">
                                <input class="form-control" type="month" value="2011-08" id="example-month-input">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="example-week-input" class="col-2 col-form-label">Week</label>
                            <div class="col-10">
                                <input class="form-control" type="week" value="2011-W33" id="example-week-input">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="example-time-input" class="col-2 col-form-label">Time</label>
                            <div class="col-10">
                                <input class="form-control" type="time" value="13:45:00" id="example-time-input">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="example-month-input2" class="col-2 col-form-label">Select</label>
                            <div class="col-10">
                                <select class="custom-select col-12" id="example-month-input2">
                                    <option selected="">Choose...</option>
                                    <option value="1">One</option>
                                    <option value="2">Two</option>
                                    <option value="3">Three</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="example-color-input" class="col-2 col-form-label">Color</label>
                            <div class="col-10">
                                <input class="form-control" type="color" value="#563d7c" id="example-color-input">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="example-color-input" class="col-2 col-form-label">Input Range</label>
                            <div class="col-10">
                                <input type="range" class="form-control" id="range" value="50">
                            </div>
                        </div> -->

                        <div class="form-group col-10 mt-5 row">
                            <button type="submit" class="btn btn-success mr-2">Save</button>
                            <a class="btn btn-dark" href="{{ route('tenants.index') }}">Cancel</a>
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