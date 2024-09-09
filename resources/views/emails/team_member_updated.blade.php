<!DOCTYPE html>
<html>
<head>
    <title>Team Member Updated</title>
</head>
<body>
    <h1>Hello, {{ $teamMember->first_name }} {{ $teamMember->last_name }}</h1>
    <p>Your account has been updated with the following details:</p>
    <ul>
        <li>Email: {{ $teamMember->email }}</li>
        <li>Role: {{ $teamMember->role->name }}</li>
    </ul>
    <p>Thank you!</p>
</body>
</html>
