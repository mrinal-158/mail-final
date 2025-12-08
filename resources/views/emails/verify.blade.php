<!DOCTYPE html>
<html>
<body>
    <h2>Hello {{ $user->name }},</h2>

    <p>Welcome to <b>SparkTech</b>!</p>

    <p>Click the link below to verify your email:</p>

    <p>
        <a href="{{ $verifyUrl }}" style="background:#0066ff;padding:10px 15px;color:white;text-decoration:none;">
            Verify Email
        </a>
    </p>

    <p>If you did not create an account, ignore this email.</p>

    <p>Thanks,<br>SparkTech Team</p>
</body>
</html>
