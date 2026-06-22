@extends('layouts.app')

@section('content')
<!-- ============================================================== -->
<!-- Bread crumb and right sidebar toggle -->
<!-- ============================================================== -->
<div class="page-breadcrumb border-bottom">
    <div class="row">
        <div class="col-lg-3 col-md-4 col-xs-12 align-self-center">
            <h5 class="font-medium text-uppercase mb-0">Platform Dashboard</h5>
        </div>
        <div class="col-lg-9 col-md-8 col-xs-12 align-self-center">
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
    <!-- Stats Cards Row  -->
    <!-- ============================================================== -->
    <div class="row">
        <!-- Tenants Card -->
        <div class="col-md-6 col-lg-3">
            <div class="card border-left border-info">
                <div class="card-body">
                    <h5 class="card-title text-uppercase text-muted">Tenants</h5>
                    <div class="d-flex align-items-center mb-2 mt-3">
                        <h2 class="mb-0 display-5 text-info"><i class="mdi mdi-office-building"></i></h2>
                        <div class="ml-auto text-right">
                            <h2 class="mb-0 font-weight-bold">{{ number_format($stats['total_tenants']) }}</h2>
                            <small class="text-success">{{ $stats['active_tenants'] }} active</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Elections Card -->
        <div class="col-md-6 col-lg-3">
            <div class="card border-left border-primary">
                <div class="card-body">
                    <h5 class="card-title text-uppercase text-muted">Elections</h5>
                    <div class="d-flex align-items-center mb-2 mt-3">
                        <h2 class="mb-0 display-5 text-primary"><i class="mdi mdi-poll"></i></h2>
                        <div class="ml-auto text-right">
                            <h2 class="mb-0 font-weight-bold">{{ number_format($stats['total_elections']) }}</h2>
                            <small class="text-warning">{{ $stats['active_elections'] }} active</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Candidates Card -->
        <div class="col-md-6 col-lg-3">
            <div class="card border-left border-danger">
                <div class="card-body">
                    <h5 class="card-title text-uppercase text-muted">Candidates</h5>
                    <div class="d-flex align-items-center mb-2 mt-3">
                        <h2 class="mb-0 display-5 text-danger"><i class="mdi mdi-account-multiple"></i></h2>
                        <div class="ml-auto text-right">
                            <h2 class="mb-0 font-weight-bold">{{ number_format($stats['total_candidates']) }}</h2>
                            <small class="text-muted">across all elections</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Voters Card -->
        <div class="col-md-6 col-lg-3">
            <div class="card border-left border-success">
                <div class="card-body">
                    <h5 class="card-title text-uppercase text-muted">Voters</h5>
                    <div class="d-flex align-items-center mb-2 mt-3">
                        <h2 class="mb-0 display-5 text-success"><i class="mdi mdi-account-check"></i></h2>
                        <div class="ml-auto text-right">
                            <h2 class="mb-0 font-weight-bold">{{ number_format($stats['total_voters']) }}</h2>
                            <small class="text-muted">registered</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Email & SMS Stats Row -->
    <div class="row mt-3">
        <!-- Emails Card -->
        <div class="col-md-6 col-lg-3">
            <div class="card bg-light-info">
                <div class="card-body">
                    <h5 class="card-title text-uppercase text-info"><i class="mdi mdi-email"></i> Emails Sent</h5>
                    <div class="row mt-3">
                        <div class="col-6 text-center border-right">
                            <h3 class="mb-0 font-weight-bold text-info">{{ number_format($emailStats['total_sent']) }}</h3>
                            <small class="text-muted">Total</small>
                        </div>
                        <div class="col-6 text-center">
                            <h3 class="mb-0 font-weight-bold text-success">{{ number_format($emailStats['successful']) }}</h3>
                            <small class="text-muted">Success</small>
                        </div>
                    </div>
                    @if($emailStats['failed'] > 0)
                    <div class="mt-2 text-center">
                        <span class="badge badge-danger">{{ $emailStats['failed'] }} failed</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        <!-- SMS Card -->
        <div class="col-md-6 col-lg-3">
            <div class="card bg-light-success">
                <div class="card-body">
                    <h5 class="card-title text-uppercase text-success"><i class="mdi mdi-message-text"></i> SMS Sent</h5>
                    <div class="row mt-3">
                        <div class="col-6 text-center border-right">
                            <h3 class="mb-0 font-weight-bold text-success">{{ number_format($smsStats['total_sent']) }}</h3>
                            <small class="text-muted">Total</small>
                        </div>
                        <div class="col-6 text-center">
                            <h3 class="mb-0 font-weight-bold text-success">{{ number_format($smsStats['successful']) }}</h3>
                            <small class="text-muted">Success</small>
                        </div>
                    </div>
                    @if($smsStats['failed'] > 0)
                    <div class="mt-2 text-center">
                        <span class="badge badge-danger">{{ $smsStats['failed'] }} failed</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        <!-- Votes Card -->
        <div class="col-md-6 col-lg-3">
            <div class="card bg-light-warning">
                <div class="card-body">
                    <h5 class="card-title text-uppercase text-warning"><i class="mdi mdi-vote"></i> Votes Cast</h5>
                    <div class="text-center mt-3">
                        <h2 class="mb-0 font-weight-bold text-warning">{{ number_format($stats['total_votes_cast']) }}</h2>
                        <small class="text-muted">{{ number_format($stats['total_ballots']) }} ballots created</small>
                    </div>
                </div>
            </div>
        </div>
        <!-- Cost Card -->
        <div class="col-md-6 col-lg-3">
            <div class="card bg-light-danger">
                <div class="card-body">
                    <h5 class="card-title text-uppercase text-danger"><i class="mdi mdi-currency-usd"></i> Total Cost</h5>
                    <div class="text-center mt-3">
                        <h2 class="mb-0 font-weight-bold text-danger">${{ number_format($costs['total_cost'], 2) }}</h2>
                        <small class="text-muted">Emails: ${{ number_format($costs['email_cost'], 2) }} | SMS: ${{ number_format($costs['sms_cost'], 2) }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mt-4">
        <!-- Monthly Activity Chart -->
        <div class="col-md-12 col-lg-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title text-uppercase">Monthly Activity</h5>
                    <div id="monthlyActivityChart" style="height: 350px"></div>
                </div>
            </div>
        </div>
        <!-- Election Status Distribution -->
        <div class="col-md-12 col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title text-uppercase">Election Status</h5>
                    <div id="electionStatusChart" style="height: 300px"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Tenants & Recent Activity Row -->
    <div class="row mt-4">
        <!-- Top Tenants -->
        <div class="col-md-12 col-lg-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title text-uppercase">Top Tenants by Activity</h5>
                    <div class="table-responsive mt-3">
                        <table class="table table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>Tenant</th>
                                    <th class="text-center">Elections</th>
                                    <th class="text-center">Voters</th>
                                    <th class="text-center">Candidates</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topTenants as $tenant)
                                <tr>
                                    <td>
                                        <strong>{{ $tenant->name }}</strong>
                                        <br><small class="text-muted">{{ $tenant->subdomain }}</small>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-info">{{ $tenant->elections_count }}</span>
                                    </td>
                                    <td class="text-center">{{ $tenant->voters_count }}</td>
                                    <td class="text-center">{{ $tenant->candidates_count }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">No tenants yet</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- Recent Elections -->
        <div class="col-md-12 col-lg-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title text-uppercase">Recent Elections</h5>
                    <div class="table-responsive mt-3">
                        <table class="table table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>Election</th>
                                    <th>Tenant</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentElections as $election)
                                <tr>
                                    <td>
                                        <strong>{{ $election->name }}</strong>
                                    </td>
                                    <td>{{ $election->tenant?->name ?? 'N/A' }}</td>
                                    <td>
                                        @switch($election->status)
                                            @case('draft')
                                                <span class="badge badge-secondary">Draft</span>
                                                @break
                                            @case('pending')
                                                <span class="badge badge-info">Pending</span>
                                                @break
                                            @case('in-progress')
                                                <span class="badge badge-warning">In Progress</span>
                                                @break
                                            @case('completed')
                                                <span class="badge badge-success">Completed</span>
                                                @break
                                            @default
                                                <span class="badge badge-secondary">{{ $election->status }}</span>
                                        @endswitch
                                    </td>
                                    <td>{{ $election->created_at->diffForHumans() }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">No elections yet</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- System Health Row -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="mdi mdi-heart-pulse"></i> System Health</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 text-center">
                            <h6>Email Delivery Rate</h6>
                            <div class="progress" style="height: 25px;">
                                <div class="progress-bar {{ $systemHealth['failed_emails_percentage'] > 10 ? 'bg-danger' : 'bg-success' }}" 
                                     role="progressbar" 
                                     style="width: {{ 100 - $systemHealth['failed_emails_percentage'] }}%"
                                     aria-valuenow="{{ 100 - $systemHealth['failed_emails_percentage'] }}" 
                                     aria-valuemin="0" 
                                     aria-valuemax="100">
                                    {{ 100 - $systemHealth['failed_emails_percentage'] }}%
                                </div>
                            </div>
                            <small class="text-muted">{{ $emailStats['successful'] }} of {{ $emailStats['total_sent'] }} delivered</small>
                        </div>
                        <div class="col-md-6 text-center">
                            <h6>SMS Delivery Rate</h6>
                            <div class="progress" style="height: 25px;">
                                <div class="progress-bar {{ $systemHealth['failed_sms_percentage'] > 10 ? 'bg-danger' : 'bg-success' }}" 
                                     role="progressbar" 
                                     style="width: {{ 100 - $systemHealth['failed_sms_percentage'] }}%"
                                     aria-valuenow="{{ 100 - $systemHealth['failed_sms_percentage'] }}" 
                                     aria-valuemin="0" 
                                     aria-valuemax="100">
                                    {{ 100 - $systemHealth['failed_sms_percentage'] }}%
                                </div>
                            </div>
                            <small class="text-muted">{{ $smsStats['successful'] }} of {{ $smsStats['total_sent'] }} delivered</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- ============================================================== -->
<!-- End Container fluid  -->
<!-- ============================================================== -->

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Monthly Activity Chart
        var monthlyChart = echarts.init(document.getElementById('monthlyActivityChart'));
        var monthlyOption = {
            tooltip: {
                trigger: 'axis',
                axisPointer: { type: 'cross' }
            },
            legend: {
                data: ['Emails', 'SMS', 'Elections', 'Votes']
            },
            grid: {
                left: '3%',
                right: '4%',
                bottom: '3%',
                containLabel: true
            },
            xAxis: {
                type: 'category',
                boundaryGap: false,
                data: @json($monthlyData['months'])
            },
            yAxis: {
                type: 'value'
            },
            series: [
                {
                    name: 'Emails',
                    type: 'line',
                    smooth: true,
                    data: @json($monthlyData['emails']),
                    itemStyle: { color: '#7460ee' }
                },
                {
                    name: 'SMS',
                    type: 'line',
                    smooth: true,
                    data: @json($monthlyData['sms']),
                    itemStyle: { color: '#36bea6' }
                },
                {
                    name: 'Elections',
                    type: 'line',
                    smooth: true,
                    data: @json($monthlyData['elections']),
                    itemStyle: { color: '#2962ff' }
                },
                {
                    name: 'Votes',
                    type: 'line',
                    smooth: true,
                    data: @json($monthlyData['votes']),
                    itemStyle: { color: '#f62d51' }
                }
            ]
        };
        monthlyChart.setOption(monthlyOption);

        // Election Status Chart
        var statusChart = echarts.init(document.getElementById('electionStatusChart'));
        var statusData = @json($electionStatusData);
        var statusOption = {
            tooltip: {
                trigger: 'item',
                formatter: '{a} <br/>{b}: {c} ({d}%)'
            },
            legend: {
                orient: 'vertical',
                left: 'left',
                data: Object.keys(statusData)
            },
            series: [
                {
                    name: 'Election Status',
                    type: 'pie',
                    radius: ['40%', '70%'],
                    avoidLabelOverlap: false,
                    itemStyle: {
                        borderRadius: 10,
                        borderColor: '#fff',
                        borderWidth: 2
                    },
                    label: {
                        show: false,
                        position: 'center'
                    },
                    emphasis: {
                        label: {
                            show: true,
                            fontSize: 20,
                            fontWeight: 'bold'
                        }
                    },
                    labelLine: {
                        show: false
                    },
                    data: Object.keys(statusData).map(function(key) {
                        return { value: statusData[key], name: key };
                    })
                }
            ]
        };
        statusChart.setOption(statusOption);

        // Responsive charts
        window.addEventListener('resize', function() {
            monthlyChart.resize();
            statusChart.resize();
        });
    });
</script>
@endpush

@endsection
