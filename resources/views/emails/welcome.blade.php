<!DOCTYPE html>
<html>
<head>
    <title>{{ $subjectText }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 50px auto;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333333;
        }
        p {
            font-size: 16px;
            line-height: 1.6;
            color: #555555;
        }
        .footer {
            margin-top: 30px;
            font-size: 12px;
            color: #aaaaaa;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome, {{ $user->name }}!</h1>
        <p>{{ $bodyText }}</p>

        <p>We are excited to have you as a new member in SparkTech.</p>

        <p>Best regards,<br>
        SparkTech Team</p>

        <div class="footer">
            &copy; {{ date('Y') }} SparkTech. All rights reserved.
        </div>
    </div>
</body>
</html>
