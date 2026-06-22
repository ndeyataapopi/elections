<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Election Results Released - {{ $election->name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 700px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background-color: #f9f9f9;
            padding: 30px;
            border: 1px solid #ddd;
            border-radius: 0 0 8px 8px;
        }
        .info-box {
            background-color: #e3f2fd;
            border-left: 4px solid #2196F3;
            padding: 20px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .results-box {
            background-color: white;
            border: 1px solid #e0e0e0;
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
        }
        .portfolio-section {
            margin-bottom: 25px;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 15px;
        }
        .portfolio-section:last-child {
            border-bottom: none;
        }
        .portfolio-title {
            color: #667eea;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 15px;
        }
        .candidate-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 15px;
            margin: 5px 0;
            background-color: #f8f9fa;
            border-radius: 4px;
            border-left: 3px solid #ddd;
        }
        .candidate-row.winner {
            background-color: #d4edda;
            border-left-color: #28a745;
            font-weight: bold;
        }
        .candidate-name {
            flex: 1;
        }
        .vote-count {
            font-weight: bold;
            color: #333;
        }
        .button {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 40px;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
            font-size: 16px;
            font-weight: bold;
            text-align: center;
        }
        .button:hover {
            opacity: 0.9;
        }
        .button-container {
            text-align: center;
            margin: 30px 0;
        }
        .warning {
            background-color: #fff3cd;
            border: 1px solid #ffc107;
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            padding: 20px;
            color: #666;
            font-size: 12px;
        }
        .stats {
            display: flex;
            justify-content: space-around;
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .stat-item {
            text-align: center;
        }
        .stat-number {
            font-size: 24px;
            font-weight: bold;
            color: #667eea;
        }
        .stat-label {
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>📊 Election Results Released</h1>
        <p>{{ $election->name }}</p>
    </div>

    <div class="content">
        <p>Dear {{ $admin->name ?? 'Administrator' }},</p>

        <p>The election results for <strong>{{ $election->name }}</strong> have been released and are awaiting your review and approval.</p>

        <div class="info-box">
            <strong>Your Role:</strong> You have been designated as an Election Approval Administrator. Your responsibility is to review the election results and confirm whether the election was conducted in a free and fair manner.
        </div>

        <div class="stats">
            <div class="stat-item">
                <div class="stat-number">{{ $results['total_voters'] ?? 0 }}</div>
                <div class="stat-label">Total Voters</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">{{ $results['total_votes_cast'] ?? 0 }}</div>
                <div class="stat-label">Votes Cast</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">{{ $results['turnout_percentage'] ?? '0%' }}</div>
                <div class="stat-label">Turnout</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">{{ $results['total_portfolios'] ?? 0 }}</div>
                <div class="stat-label">Portfolios</div>
            </div>
        </div>

        <h3>Election Results Summary</h3>

        <div class="results-box">
            @foreach($results['portfolios'] ?? [] as $portfolio)
            <div class="portfolio-section">
                <div class="portfolio-title">{{ $portfolio['name'] }}</div>
                @foreach($portfolio['candidates'] ?? [] as $candidate)
                <div class="candidate-row {{ $candidate['is_winner'] ? 'winner' : '' }}">
                    <span class="candidate-name">
                        {{ $candidate['name'] }}
                        @if($candidate['is_winner'])
                            <span style="color: #28a745; margin-left: 10px;">🏆 Winner</span>
                        @endif
                    </span>
                    <span class="vote-count">{{ $candidate['votes'] }} votes ({{ $candidate['percentage'] }}%)</span>
                </div>
                @endforeach
            </div>
            @endforeach
        </div>

        <div class="warning">
            <strong>⚠️ Action Required:</strong>
            <p>Please review the results above and click the button below to confirm your approval or rejection. Once all designated administrators have approved the results, the election will be officially certified.</p>
        </div>

        <div class="button-container">
            <a href="{{ $approvalUrl }}" class="button">Review & Approve Results</a>
        </div>

        <p>If the button doesn't work, copy and paste this link into your browser:</p>
        <p style="word-break: break-all; font-size: 12px; color: #666; background: #f4f4f4; padding: 10px; border-radius: 4px;">{{ $approvalUrl }}</p>

        <p>Thank you for your service in ensuring the integrity of our electoral process.</p>

        <p>Best regards,<br>
        Election Management Team</p>
    </div>

    <div class="footer">
        <p>This is an automated message. Please do not reply to this email.</p>
        <p>If you did not expect this email, please contact the election administrator immediately.</p>
    </div>
</body>
</html>
