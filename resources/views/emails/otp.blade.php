<!DOCTYPE html>
<html>
<head>
    <title>Password Reset OTP</title>
</head>
<body style="font-family: sans-serif; line-height: 1.6; color: #333;">
    <h2>Password Reset Request</h2>
    <p>You requested a password reset for your account. Please use the following 6-digit OTP to reset your password:</p>
    <div style="font-size: 24px; font-weight: bold; letter-spacing: 5px; padding: 15px; background: #f3f4f6; display: inline-block; border-radius: 8px;">
        {{ $otp }}
    </div>
    <p>If you did not request this, you can safely ignore this email.</p>
</body>
</html>
