<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Voting Link - {{ $election->name ?? 'Upcoming Election' }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #28a745;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f9f9f9;
            padding: 30px;
            border: 1px solid #ddd;
        }
        .button {
            display: inline-block;
            background-color: #28a745;
            color: white;
            padding: 15px 40px;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
            font-size: 16px;
            font-weight: bold;
        }
        .button:hover {
            background-color: #218838;
        }
        .footer {
            text-align: center;
            padding: 20px;
            color: #666;
            font-size: 12px;
        }
        .election-info {
            background-color: #e8f5e9;
            border: 1px solid #4caf50;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .token-box {
            background-color: #fff3cd;
            border: 1px solid #ffc107;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            word-break: break-all;
        }
        .warning {
            background-color: #f8d7da;
            border: 1px solid #dc3545;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🗳️ Cast Your Vote</h1>
        <p>{{ $election->name ?? 'Upcoming Election' }}</p>
    </div>
    
    <div class="content">
        <p>Dear {{ $voter->name ?? 'Voter' }},</p>
        
        <p>The election <strong>{{ $election->name ?? 'Upcoming Election' }}</strong> is now open for voting!</p>
        
        <div class="election-info">
            <p><strong>Election Period:</strong></p>
            <p>Start: {{ $election->start_date ?? now()->format('F j, Y') }}</p>
            <p>End: {{ $election->end_date ?? now()->addDays(3)->format('F j, Y') }}</p>
        </div>
        
        <p>Click the button below to access your secure voting page:</p>
        
        <div style="text-align: center;">
            <a href="{{ $votingUrl }}" class="button">Cast My Vote</a>
        </div>
        
        <div class="token-box">
            <strong>Your Secure Voting Token:</strong><br>
            <code style="font-size: 14px;">{{ $token }}</code>
            <p style="font-size: 12px; margin-top: 10px;">This token is unique to you and ensures your vote is recorded securely.</p>
        </div>
        
        <div class="warning">
            <strong>Important:</strong>
            <ul>
                <li>You can only vote once - make sure to review all candidates before submitting</li>
                <li>Your vote is anonymous and cannot be changed after submission</li>
                <li>Voting closes on {{ $election->end_date ?? now()->addDays(3)->format('F j, Y') }}</li>
            </ul>
        </div>
        
        <p>If the button doesn't work, copy and paste this link into your browser:</p>
        <p style="word-break: break-all; font-size: 12px; color: #666;">{{ $votingUrl }}</p>
        
        <p>Thank you for participating in the democratic process!</p>
        
        <p>Best regards,<br>
        Election Management Team</p>
    </div>
    
    <div class="footer">
        <p>This is an automated message. Please do not reply to this email.</p>
        <p>If you did not request this voting link, please ignore this email.</p>
    </div>
</body>
</html>
