@extends('layouts.app')

@section('content')
<!-- ============================================================== -->
<!-- Bread crumb and right sidebar toggle -->
<!-- ============================================================== -->
<div class="page-breadcrumb border-bottom">
    <div class="row">
        <div class="col-lg-3 col-md-4 col-xs-12 align-self-center">
            <h5 class="font-medium text-uppercase mb-0">Dashboard</h5>
        </div>
        <div class="col-lg-9 col-md-8 col-xs-12 align-self-center">
            <!-- <button class="btn btn-danger text-white float-right ml-3 d-none d-md-block">Buy Ample Admin</button> -->
            <nav aria-label="breadcrumb" class="mt-2 float-md-right float-left">
                <ol class="breadcrumb mb-0 justify-content-end p-0">
                    <li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
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
        <div class="col-md-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title text-uppercase">Users</h5>
                    <div class="d-flex align-items-center mb-2 mt-4">
                        <h2 class="mb-0 display-5"><i class="icon-people text-info"></i></h2>
                        <div class="ml-auto">
                            <h2 class="mb-0 display-6"><span class="font-normal">{{ $usersCount }}</span></h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title text-uppercase">Elections</h5>
                    <div class="d-flex align-items-center mb-2 mt-4">
                        <h2 class="mb-0 display-5"><i class="icon-folder text-primary"></i></h2>
                        <div class="ml-auto">
                            <h2 class="mb-0 display-6"><span class="font-normal">{{ $electionsCount }}</span></h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title text-uppercase">Candidates</h5>
                    <div class="d-flex align-items-center mb-2 mt-4">
                        <h2 class="mb-0 display-5"><i class="icon-folder-alt text-danger"></i></h2>
                        <div class="ml-auto">
                            <h2 class="mb-0 display-6"><span class="font-normal">{{ $candidatesCount }}</span></h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title text-uppercase">Voters</h5>
                    <div class="d-flex align-items-center mb-2 mt-4">
                        <h2 class="mb-0 display-5"><i class="ti-wallet text-success"></i></h2>
                        <div class="ml-auto">
                            <h2 class="mb-0 display-6"><span class="font-normal">{{ $votersCount }}</span></h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- ============================================================== -->
    <!-- Election Statistics Charts Row  -->
    <!-- ============================================================== -->
    <div class="row">
        <div class="col-md-12 col-lg-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title text-uppercase">Candidates & Voters per Election</h5>
                    <div style="height: 300px;">
                        <canvas id="electionChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12 col-lg-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title text-uppercase">Election Participation Overview</h5>
                    <div style="height: 300px;">
                        <canvas id="participationChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- ============================================================== -->
    <!-- Recent Elections Table Row  -->
    <!-- ============================================================== -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title text-uppercase mb-0">Recent Elections</h5>
                </div>
                <div class="table-responsive">
                    <table class="table no-wrap user-table mb-0">
                      <thead>
                        <tr>
                          <th scope="col" class="border-0 text-uppercase font-medium pl-4">#</th>
                          <th scope="col" class="border-0 text-uppercase font-medium">Election Name</th>
                          <th scope="col" class="border-0 text-uppercase font-medium">Candidates</th>
                          <th scope="col" class="border-0 text-uppercase font-medium">Voters</th>
                          <th scope="col" class="border-0 text-uppercase font-medium">Start Date</th>
                          <th scope="col" class="border-0 text-uppercase font-medium">End Date</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($elections->take(5) as $index => $election)
                        <tr>
                          <td class="pl-4">{{ $index + 1 }}</td>
                          <td>
                              <h5 class="font-medium mb-0">{{ $election->name }}</h5>
                          </td>
                          <td>
                              <span class="badge badge-info">{{ $election->candidates_count }}</span>
                          </td>
                          <td>
                              <span class="badge badge-success">{{ $election->voters_count }}</span>
                          </td>
                          <td>
                              <span class="text-muted">{{ \Carbon\Carbon::parse($election->start_time)->format('d M Y H:i') }}</span>
                          </td>
                          <td>
                              <span class="text-muted">{{ \Carbon\Carbon::parse($election->end_time)->format('d M Y H:i') }}</span>
                          </td>
                        </tr>
                        @endforeach
                      </tbody>
                    </table>
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
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Election Chart - Candidates & Voters per Election
    const electionCtx = document.getElementById('electionChart').getContext('2d');
    new Chart(electionCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($electionNames) !!},
            datasets: [
                {
                    label: 'Candidates',
                    data: {!! json_encode($electionCandidates) !!},
                    backgroundColor: 'rgba(54, 162, 235, 0.8)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Voters',
                    data: {!! json_encode($electionVoters) !!},
                    backgroundColor: 'rgba(75, 192, 192, 0.8)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            },
            plugins: {
                legend: {
                    position: 'top',
                }
            }
        }
    });

    // Participation Chart - Pie chart showing overall participation
    const totalCandidates = {!! json_encode($candidatesCount) !!};
    const totalVoters = {!! json_encode($votersCount) !!};
    
    const participationCtx = document.getElementById('participationChart').getContext('2d');
    new Chart(participationCtx, {
        type: 'doughnut',
        data: {
            labels: ['Candidates', 'Voters'],
            datasets: [{
                data: [totalCandidates, totalVoters],
                backgroundColor: [
                    'rgba(54, 162, 235, 0.8)',
                    'rgba(75, 192, 192, 0.8)'
                ],
                borderColor: [
                    'rgba(54, 162, 235, 1)',
                    'rgba(75, 192, 192, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                }
            }
        }
    });
</script>
@endpush
