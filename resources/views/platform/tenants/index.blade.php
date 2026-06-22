@extends('layouts.app')

@section('title', 'Tenants')

@php
use Illuminate\Support\Facades\Storage;
@endphp

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

                    @if(Session::has('success_message'))
                    <div class="alert alert-success alert-dismissable">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        {{ Session::get('success_message') }}
                    </div>
                    @endif

                    @if(Session::has('error_message'))
                    <div class="alert alert-danger alert-dismissable">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        {{ Session::get('error_message') }}
                    </div>
                    @endif
                    
                    <h4 class="card-title text-uppercase mb-0"> Tenants
                        <span class="pull-right">
                            <a class="btn btn-outline-info btn-sm" href="{{ route('tenants.create') }}">
                            + New Tenant </a>
                        </span>
                    </h4><br>
                    <h5 class="card-subtitle">All Tenants and their Status</h5>

                    <div class="table-responsive">
                        <!-- <table id="zero_config" class="table table-striped border"> -->
                        <table id="config-table" class="table display table-bordered table-striped no-wrap border">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Sub-domain</th>
                                    <th>Logo</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tenants as $tenant)
                                <tr>
                                    <td>{{ $tenant->name }}</td>
                                    <td><a href="http://{{ $tenant->subdomain }}.elections.test" target="_blank" rel="noopener noreferrer">{{ $tenant->subdomain }}.elections.test</a></td>
                                    <td>
                                        @if($tenant->logo)
                                            <img src="{{ Storage::url($tenant->logo) }}" alt="{{ $tenant->name }} logo" style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px; border: 1px solid #ddd;">
                                        @else
                                            <span class="text-muted">No logo</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($tenant->status == 1)
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-secondary">In-Active</span>
                                        @endif
                                    </td>
                                    <td> 
                                        <a href="{{ route('tenants.edit', $tenant->id) }}" class="btn btn-sm btn-primary text-white mr-1">Edit</a>
                                        
                                        @if($tenant->status == 1)
                                            <form action="{{ route('tenants.toggle', $tenant->id) }}" method="POST" style="display: inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-warning text-white" onclick="return confirm('Are you sure you want to deactivate this tenant?')">Disable</button>
                                            </form>
                                        @else
                                            <form action="{{ route('tenants.toggle', $tenant->id) }}" method="POST" style="display: inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success text-white" onclick="return confirm('Are you sure you want to activate this tenant?')">Enable</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Name</th>
                                    <th>Subdomain</th>
                                    <th>Logo</th>
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