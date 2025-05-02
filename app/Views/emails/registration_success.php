<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Welcome to Our Platform</title>
    <link href="https://fonts.googleapis.com/css2?family=PT+Sans&display=swap" rel="stylesheet">
</head>
<body style="font-family: 'PT Sans', sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h2 style="color: #2c3e50;">Welcome, <?= $name ?>!</h2>
        
        <p>Thank you for registering with our platform. Your account has been successfully created.</p>
        
        <p>Your account details:</p>
        <ul>
            <li>Name: <?= $name ?></li>
            <li>Email: <?= $email ?></li>
        </ul>
        
        <p>You can now log in to your account using your email and password.</p>
        
        <p>If you did not create this account, please contact our support team immediately.</p>
        
        <p>Best regards,<br>The Team</p>
    </div>
</body>
</html>
