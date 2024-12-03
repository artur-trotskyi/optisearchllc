<!DOCTYPE html>
<html>
<head>
    <title>Verify Your Email Address</title>
</head>
<body>
<h1>Hello, {{ $user->name }}</h1>
<p>
    Thank you for registering. Please verify your email address by clicking the button below:
</p>
<p>
    <a href="{{ $verifyUrl }}" style="display: inline-block; padding: 10px 20px; color: #fff; background-color: #007bff; text-decoration: none; border-radius: 5px;">
        Verify Email Address
    </a>
</p>
<p>If you did not create this account, no further action is required.</p>
</body>
</html>
