<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Email - {{ $tenant->name }}</title>
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
            background-color: #007bff;
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
        .success-box {
            background-color: #e8f5e9;
            border: 1px solid #4caf50;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
            text-align: center;
        }
        .success-icon {
            font-size: 48px;
            color: #4caf50;
            margin-bottom: 10px;
        }
        .info-box {
            background-color: #e3f2fd;
            border: 1px solid #2196f3;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            padding: 20px;
            color: #666;
            font-size: 12px;
        }
        .details {
            background-color: #fff;
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .details table {
            width: 100%;
            border-collapse: collapse;
        }
        .details td {
            padding: 8px;
            border-bottom: 1px solid #eee;
        }
        .details td:first-child {
            font-weight: bold;
            color: #666;
            width: 40%;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>📧 Email System Test</h1>
        <p>{{ $tenant->name }} Election System</p>
    </div>
    
    <div class="content">
        <div class="success-box">
            <div class="success-icon">✅</div>
            <h2>Test Email Successful!</h2>
            <p>This confirms that your email notifications are configured correctly.</p>
        </div>
        
        <p>Hello,</p>
        
        <p>This is a test email sent from the <strong>{{ $tenant->name }}</strong> election system to verify that email notifications are working properly.</p>
        
        <div class="info-box">
            <strong>📋 What does this mean?</strong>
            <ul style="margin: 10px 0;">
                <li>Your SMTP settings are configured correctly</li>
                <li>Emails can be sent to candidates and voters</li>
                <li>Notification system is ready for use</li>
            </ul>
        </div>
        
        <div class="details">
            <h3>Email Details</h3>
            <table>
                <tr>
                    <td>Sent To:</td>
                    <td>{{ $recipientEmail }}</td>
                </tr>
                <tr>
                    <td>Sent At:</td>
                    <td>{{ now()->format('F j, Y \a\t g:i A') }}</td>
                </tr>
                <tr>
                    <td>From Tenant:</td>
                    <td>{{ $tenant->name }}</td>
                </tr>
                <tr>
                    <td>Sender IP:</td>
                    <td>{{ request()->ip() }}</td>
                </tr>
            </table>
        </div>
        
        <div class="info-box" style="background-color: #fff3cd; border-color: #ffc107;">
            <strong>⚡ Next Steps:</strong>
            <ul style="margin: 10px 0;">
                <li>Configure your notification preferences in the system</li>
                <li>Test SMS notifications if needed</li>
                <li>Start sending notifications to candidates and voters</li>
            </ul>
        </div>
        
        <p>If you received this email, everything is set up correctly and you're ready to send notifications!</p>
        
        <p>Best regards,<br>
        <strong>{{ $tenant->name }} Election System</strong></p>
    </div>
    
    <div class="footer">
        <p>This is an automated test message. Please do not reply to this email.</p>
        <p>© {{ date('Y') }} {{ $tenant->name }}. All rights reserved.</p>
    </div>
</body>
</html>
