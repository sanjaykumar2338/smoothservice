<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to the Team</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            background-color: #4CAF50;
            padding: 10px 0;
            color: white;
            border-radius: 8px 8px 0 0;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
        }

        .content {
            padding: 20px;
            text-align: left;
            color: #333;
        }

        .content p {
            margin: 10px 0;
            line-height: 1.6;
        }

        .button {
            text-align: center;
            margin: 20px 0;
        }

        .button a {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
        }

        .footer {
            text-align: center;
            font-size: 12px;
            color: #888;
            padding: 10px 0;
            border-top: 1px solid #ddd;
            margin-top: 20px;
        }

        .footer p {
            margin: 5px 0;
        }
    </style>
</head>
<body>

<div class="container">
    <!-- Header -->
    <div class="header">
        <h1>Welcome to the Team!</h1>
    </div>

    <!-- Content -->
    <div class="content">
        <p>Hello, <strong>{{ $teamMember->first_name }} {{ $teamMember->last_name }}</strong>,</p>
        <p>We’re thrilled to have you join us as a <strong>{{ $roleName }}</strong>. Your role is important, and we can't wait to see the great things you'll accomplish with us.</p>
        <p>Your login details are as follows:</p>
        <ul>
            <li><strong>Email:</strong> {{ $teamMember->email }}</li>
            <li><strong>Password:</strong> {{ $password }}</li> <!-- Instruct them to change their password after login -->
        </ul>
        <p>Please log in using the button below:</p>

        <!-- Button -->
        <div class="button">
            <a href="{{ url('/login') }}" target="_blank">Login to Your Account</a>
        </div>

        <p>Feel free to reach out if you have any questions. We're here to support you every step of the way.</p>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Thank you for joining the team!</p>
        <p>The Team</p>
    </div>
</div>

</body>
</html>
