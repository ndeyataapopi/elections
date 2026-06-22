@extends('layouts.app')

@section('title', 'Edit Portfolio')

@section('content')
<!-- ============================================================== -->
<!-- Bread crumb and right sidebar toggle -->
<!-- ============================================================== -->
<div class="page-breadcrumb border-bottom">
    <div class="row">
        <div class="col-lg-3 col-md-4 col-xs-12 align-self-center">
            <h5 class="font-medium text-uppercase mb-0">Portfolios</h5>
        </div>
        <div class="col-lg-9 col-md-8 col-xs-12 align-self-center">
            <nav aria-label="breadcrumb" class="mt-2 float-md-right float-left">
                <ol class="breadcrumb mb-0 justify-content-end p-0">
                    <li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('portfolios.index') }}">Portfolios</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('portfolios.view', $portfolio->election_id) }}">{{ $portfolio->election->name }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit Portfolio</li>
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
                    <h4 class="card-title">Edit Portfolio</h4>
                    <h5 class="card-subtitle">Update Portfolio Information</h5>
                    
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

                    <form data-toggle="validator" enctype="multipart/form-data" class="form" method="POST" action="{{ route('portfolios.update', $portfolio->id) }}">
                        @csrf

                        <div class="form-group mt-5 row">
                            <label for="example-text-input" class="col-2 col-form-label"><b>Portfolio Details</b></label>
                        </div>

                        <div class="form-group row">
                            <label for="example-text-input" class="col-2 col-form-label">Portfolio Name</label>
                            <div class="col-10">
                                <input class="form-control input" type="text" name="name" value="{{ $portfolio->name }}" placeholder="Portfolio Name" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="example-text-input" class="col-2 col-form-label">Max Number of Votes Allowed</label>
                            <div class="col-10">
                                <input class="form-control input" type="number" name="max_votes" value="{{ $portfolio->max_votes }}" placeholder="Enter Maximum Number of Votes Allowed for this Portfolio" required min="1">
                            </div>
                        </div>

                        <div class="form-group col-10 mt-5 row">
                            <button type="submit" class="btn btn-success mr-2">Update Portfolio</button>
                            <a class="btn btn-dark" href="{{ route('portfolios.view', $portfolio->election_id) }}">Cancel</a>
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
