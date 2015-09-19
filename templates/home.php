<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Fitbit!</title>
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h1>Home</h1>
        <p>Welcome <?= $user->fullName ?>! <a href="/logout">Logout</a></p>
    </div>
</body>
</html>