@extends('layouts.app')

@section('title', 'Edit Tenant')

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
                    <h4 class="card-title">Edit Tenant</h4>
                    <h5 class="card-subtitle">Complete All Required fields below</h5>
                    
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
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

                    <form class="form" data-toggle="validator" enctype="multipart/form-data" method="POST" action="{{ route('tenants.update', $tenant->id) }}">
                        @csrf
                        
                        <div class="form-group mt-5 row">
                            <label for="example-text-input" class="col-2 col-form-label"><b>Tenant Details</b></label>
                        </div>

                        <div class="form-group row">
                            <label for="example-text-input" class="col-2 col-form-label">Tenant Name</label>
                            <div class="col-10">
                                <input class="form-control input" type="text" name="name" value="{{ $tenant->name }}" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="example-search-input" class="col-2 col-form-label">Subdomain</label>
                            <div class="col-10">
                                <input class="form-control input" type="text" name="subdomain" value="{{ $tenant->subdomain }}" placeholder="Subdomain (e.g elections)" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="example-search-input" class="col-2 col-form-label">Logo</label>
                            <div class="col-10">
                                <input class="form-control input" type="file" name="image">
                                <small class="form-text text-muted">Leave empty to keep current logo</small>
                            </div>
                        </div>

                        <!-- <hr>

                        <div class="form-group row">
                            <label for="example-text-input" class="col-2 col-form-label"><b>Tenant Admin Details</b></label>
                        </div>

                        <div class="form-group row">
                            <label for="example-name-input" class="col-2 col-form-label">Admin Name</label>
                            <div class="col-10">
                                <input class="form-control" type="email" name="admin_name" placeholder="Admin Name" required id="example-name-input">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="example-email-input" class="col-2 col-form-label">Admin Email</label>
                            <div class="col-10">
                                <input class="form-control" type="email" name="admin_email" placeholder="Admin Email" required id="example-email-input">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="example-password-input" class="col-2 col-form-label">Password</label>
                            <div class="col-10">
                                <input class="form-control" type="password" name="admin_password" Placeholder="Password" value="password" id="example-password-input">
                            </div>
                        </div> -->

                        <div class="form-group col-10 mt-5 row">
                            <button type="submit" class="btn btn-success mr-2">Update</button>
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