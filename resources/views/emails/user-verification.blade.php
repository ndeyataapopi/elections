<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to the Elections System</title>
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
            background: #007bff;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background: #f9f9f9;
            padding: 30px;
            border: 1px solid #ddd;
            border-top: none;
        }
        .button {
            display: inline-block;
            background: #28a745;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
        .footer {
            background: #f1f1f1;
            padding: 20px;
            text-align: center;
            border: 1px solid #ddd;
            border-top: none;
            border-radius: 0 0 5px 5px;
            font-size: 12px;
            color: #666;
        }
        .password-box {
            background: #fff;
            border: 2px solid #007bff;
            padding: 15px;
            margin: 15px 0;
            border-radius: 5px;
            text-align: center;
        }
        .password-box strong {
            font-size: 18px;
            color: #007bff;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Welcome to Elections System</h1>
    </div>
    
    <div class="content">
        <h2>Hello {{ $user->name }},</h2>
        
        <p>Your account has been created successfully in the Elections System. Below are your login credentials:</p>
        
        <div class="password-box">
            <p><strong>Your Temporary Password:</strong></p>
            <p><strong>{{ $password }}</strong></p>
        </div>
        
        <p><strong>Email:</strong> {{ $user->email }}</p>
        <p><strong>Role:</strong> {{ ucfirst(str_replace('_', ' ', $user->role)) }}</p>
        
        <p>Please click the button below to verify your email address and set up your account:</p>
        
        <center>
            <a href="{{ $verificationUrl }}" class="button">Verify Email & Set Password</a>
        </center>
        
        <p><strong>Important:</strong></p>
        <ul>
            <li>Please verify your email within 24 hours</li>
            <li>You will be prompted to change your temporary password after verification</li>
            <li>Keep your login credentials secure</li>
        </ul>
        
        <p>If you did not request this account, please ignore this email.</p>
        
        <p>If the button above doesn't work, you can copy and paste this link into your browser:</p>
        <p><small>{{ $verificationUrl }}</small></p>
    </div>
    
    <div class="footer">
        <p>&copy; {{ date('Y') }} Elections System. All rights reserved.</p>
        <p>This is an automated message, please do not reply to this email.</p>
    </div>
</body>
</html>
