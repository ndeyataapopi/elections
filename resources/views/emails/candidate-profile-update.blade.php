<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Your Candidate Profile</title>
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
            background-color: #2cabe3;
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
            background-color: #2cabe3;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            padding: 20px;
            color: #666;
            font-size: 12px;
        }
        .deadline {
            background-color: #fff3cd;
            border: 1px solid #ffc107;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
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
        <h1>Candidate Profile Update Required</h1>
    </div>
    
    <div class="content">
        <p>Dear {{ $candidate->first_name ?? 'Candidate' }},</p>
        
        <p>You have been registered as a candidate for the <strong>{{ $candidate->election->name ?? 'Upcoming Election' }}</strong>.</p>
        
        <p>To complete your candidacy, please update your profile information by clicking the button below:</p>
        
        <div style="text-align: center;">
            <a href="{{ $editUrl }}" class="button">Update My Profile</a>
        </div>
        
        <div class="deadline">
            <strong>Deadline:</strong> Please complete your profile update before <strong>{{ $deadline }}</strong>.
            After this date, the link will expire and you will no longer be able to update your profile.
        </div>
        
        <div class="warning">
            <strong>Important:</strong> This is a one-time use link. Once you submit your profile update, you will not be able to make further changes.
        </div>
        
        <p>If the button above doesn't work, copy and paste this link into your browser:</p>
        <p style="word-break: break-all; font-size: 12px; color: #666;">{{ $editUrl }}</p>
        
        <p>If you have any questions, please contact the election administrator.</p>
        
        <p>Best regards,<br>
        Election Management Team</p>
    </div>
    
    <div class="footer">
        <p>This is an automated message. Please do not reply to this email.</p>
    </div>
</body>
</html>
