@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
<!-- ============================================================== -->
<!-- Bread crumb and right sidebar toggle -->
<!-- ============================================================== -->
<div class="page-breadcrumb border-bottom">
    <div class="row">
        <div class="col-lg-3 col-md-4 col-xs-12 align-self-center">
            <h5 class="font-medium text-uppercase mb-0">Users</h5>
        </div>
        <div class="col-lg-9 col-md-8 col-xs-12 align-self-center">
            <!-- <button class="btn btn-danger text-white float-right ml-3 d-none d-md-block">Buy Ample Admin</button> -->
            <nav aria-label="breadcrumb" class="mt-2 float-md-right float-left">
                <ol class="breadcrumb mb-0 justify-content-end p-0">
                    <li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Users</li>
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
                    <h4 class="card-title">Edit User</h4>
                    <h5 class="card-subtitle">Update User Information</h5>
                    
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

                    <form data-toggle="validator" enctype="multipart/form-data" class="form" method="POST" action="{{ route('users.update', $user->id) }}">
                        @csrf

                        <div class="form-group mt-5 row">
                            <label for="example-text-input" class="col-2 col-form-label"><b>User Details</b></label>
                        </div>

                        <div class="form-group row" id="tenant-field">
                            <label for="example-text-input" class="col-2 col-form-label">Tenant</label>
                            <div class="col-10">
                                <select class="form-control" name="tenant_id" id="tenant-select">
                                    <option value="">Select Tenant</option>
                                    @foreach($tenants as $tenant)
                                        <option value="{{ $tenant->id }}" {{ $user->tenant_id == $tenant->id ? 'selected' : '' }}>{{ $tenant->name }} ({{ $tenant->subdomain }})</option>
                                    @endforeach
                                </select>
                                <small class="form-text text-muted">Required for Client Admin users only</small>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="example-text-input" class="col-2 col-form-label">User Role</label>
                            <div class="col-10">
                                <select class="form-control" name="role" required>
                                    <option value="">Select Role</option>
                                    <option value="super_admin" {{ $user->role == 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                                    <option value="client_admin" {{ $user->role == 'client_admin' ? 'selected' : '' }}>Client Admin</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="example-name-input" class="col-2 col-form-label">Full Name</label>
                            <div class="col-10">
                                <input class="form-control" type="text" name="name" value="{{ $user->name }}" required id="example-name-input">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="example-email-input" class="col-2 col-form-label">Email Address</label>
                            <div class="col-10">
                                <input class="form-control" type="email" name="email" value="{{ $user->email }}" required id="example-email-input">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="example-password-input" class="col-2 col-form-label">Password</label>
                            <div class="col-10">
                                <input class="form-control" type="password" name="password" placeholder="Leave empty to keep current password" id="example-password-input">
                                <small class="form-text text-muted">Only enter a new password if you want to change it</small>
                            </div>
                        </div>

                        <div class="form-group col-10 mt-5 row">
                            <button type="submit" class="btn btn-success mr-2">Update User</button>
                            <a class="btn btn-dark" href="{{ route('users.index') }}">Cancel</a>
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
    $(document).ready(function() {
        // Function to toggle tenant field visibility
        function toggleTenantField() {
            var selectedRole = $('select[name="role"]').val();
            var tenantField = $('#tenant-field');
            
            if (selectedRole === 'client_admin') {
                tenantField.show();
                $('#tenant-select').prop('required', true);
            } else {
                tenantField.hide();
                $('#tenant-select').prop('required', false).val('');
            }
        }
        
        // Initial check
        toggleTenantField();
        
        // Handle role change
        $('select[name="role"]').on('change', function() {
            toggleTenantField();
        });
    });
</script>
@endpush