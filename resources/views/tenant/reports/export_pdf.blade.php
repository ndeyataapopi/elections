<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Election Results - {{ $election->name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #333;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 24px;
            margin: 0;
            color: #2c3e50;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .section {
            margin-bottom: 20px;
        }
        .section-title {
            font-size: 16px;
            font-weight: bold;
            background-color: #f4f4f4;
            padding: 8px;
            margin-bottom: 10px;
            border-left: 4px solid #2c3e50;
        }
        .info-table {
            width: 100%;
            margin-bottom: 15px;
        }
        .info-table td {
            padding: 5px 10px;
        }
        .info-table td:first-child {
            font-weight: bold;
            width: 30%;
        }
        .summary-box {
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 20px;
        }
        .summary-grid {
            display: flex;
            justify-content: space-between;
        }
        .summary-item {
            text-align: center;
        }
        .summary-item h3 {
            font-size: 24px;
            margin: 0;
            color: #2c3e50;
        }
        .summary-item p {
            margin: 5px 0;
            color: #666;
        }
        .portfolio-section {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }
        .portfolio-title {
            font-size: 14px;
            font-weight: bold;
            background-color: #34495e;
            color: white;
            padding: 8px;
            margin-bottom: 10px;
        }
        .results-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .results-table th {
            background-color: #ecf0f1;
            padding: 8px;
            text-align: left;
            border-bottom: 2px solid #bdc3c7;
        }
        .results-table td {
            padding: 8px;
            border-bottom: 1px solid #ecf0f1;
        }
        .results-table tr.winner {
            background-color: #d5f4e6;
            font-weight: bold;
        }
        .winner-badge {
            background-color: #27ae60;
            color: white;
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 10px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            font-size: 10px;
            color: #666;
        }
        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: bold;
        }
        .status-draft { background-color: #95a5a6; color: white; }
        .status-pending { background-color: #3498db; color: white; }
        .status-in-progress { background-color: #f39c12; color: white; }
        .status-completed { background-color: #27ae60; color: white; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Election Results Report</h1>
        <p>{{ $election->name }}</p>
        <p>Generated on {{ $generated_at }}</p>
    </div>

    <div class="section">
        <div class="section-title">Election Information</div>
        <table class="info-table">
            <tr>
                <td>Election Name:</td>
                <td>{{ $election->name }}</td>
            </tr>
            <tr>
                <td>Description:</td>
                <td>{{ $election->description ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>Status:</td>
                <td>
                    <span class="status-badge status-{{ $election->status }}">
                        {{ ucfirst($election->status) }}
                    </span>
                </td>
            </tr>
            <tr>
                <td>Start Date:</td>
                <td>{{ $election->start_date?->format('F d, Y H:i') ?? 'Not set' }}</td>
            </tr>
            <tr>
                <td>End Date:</td>
                <td>{{ $election->end_date?->format('F d, Y H:i') ?? 'Not set' }}</td>
            </tr>
            <tr>
                <td>Tenant:</td>
                <td>{{ $election->tenant?->name ?? 'N/A' }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Voting Summary</div>
        <div class="summary-box">
            <div class="summary-grid">
                <div class="summary-item">
                    <h3>{{ number_format($results['total_voters']) }}</h3>
                    <p>Registered Voters</p>
                </div>
                <div class="summary-item">
                    <h3>{{ number_format($results['votes_cast']) }}</h3>
                    <p>Votes Cast</p>
                </div>
                <div class="summary-item">
                    <h3>{{ $results['turnout_percentage'] }}%</h3>
                    <p>Voter Turnout</p>
                </div>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Results by Portfolio</div>
        
        @foreach($results['portfolio_results'] as $portfolioResult)
        <div class="portfolio-section">
            <div class="portfolio-title">{{ $portfolioResult['portfolio']->name }}</div>
            
            <table class="results-table">
                <thead>
                    <tr>
                        <th>Candidate</th>
                        <th>Email</th>
                        <th style="text-align: center;">Votes</th>
                        <th style="text-align: center;">Percentage</th>
                        <th style="text-align: center;">Result</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($portfolioResult['candidates'] as $candidate)
                    <tr class="{{ $candidate['is_winner'] ? 'winner' : '' }}">
                        <td>{{ $candidate['name'] }}</td>
                        <td>{{ $candidate['candidate']->email ?? 'N/A' }}</td>
                        <td style="text-align: center;">{{ number_format($candidate['votes']) }}</td>
                        <td style="text-align: center;">{{ $candidate['percentage'] }}%</td>
                        <td style="text-align: center;">
                            @if($candidate['is_winner'])
                                <span class="winner-badge">WINNER</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            
            @if($portfolioResult['winner'])
            <p><strong>Winner:</strong> {{ $portfolioResult['winner']['name'] }} with {{ number_format($portfolioResult['winner']['votes']) }} votes ({{ $portfolioResult['winner']['percentage'] }}%)</p>
            @else
            <p><em>No votes cast for this portfolio</em></p>
            @endif
        </div>
        @endforeach
    </div>

    <div class="footer">
        <p>Election Results Report generated by {{ config('app.name') }}</p>
        <p>This is an official election document. For questions, contact the election administrator.</p>
    </div>
</body>
</html>
