<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You for Voting</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }
        .container {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .thank-you-container {
            max-width: 600px;
            text-align: center;
            padding: 40px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .success-icon {
            font-size: 80px;
            color: #28a745;
            margin-bottom: 20px;
        }
        .home-btn {
            background: linear-gradient(135deg, #0B1F3A 0%, #1a3a5c 100%);
            border: none;
            padding: 12px 40px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="thank-you-container">
            <div class="success-icon">✅</div>
            <h2 class="mb-4">Thank You for Voting!</h2>
            <p class="text-muted mb-4">Your vote has been securely recorded. Your participation in the democratic process is greatly appreciated.</p>
            <div class="alert alert-info">
                <strong>Reference:</strong> Your vote has been anonymized for privacy protection.
            </div>
            <!-- <a href="/" class="btn btn-primary home-btn mt-3">Return to Home</a> -->
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
