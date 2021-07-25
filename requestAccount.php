<?php
if(isset($_REQUEST['username']) and isset($_REQUEST['password'])){
    $hashed_password = hash('sha256',$_REQUEST['password']);

    require("SQLconnect.php");
    $stmt = $conn->prepare('INSERT INTO pending_atc_login_data (username,hashed_password) VALUES (?, ?)');
    $stmt->bind_param('ss',$_REQUEST['username'],$hashed_password);
    $stmt->execute();
    echo '<script>alert("Accounht information submitted successfully, you will be contacted on discord when your account will be activated (within 24 hours)")</script>';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Document</title>
</head>
<body style="text-align: center;" >
    <div>
        <form method="post" action="requestAccount.php">
            <label for="username">Username (has to match username in PTFS server):</label>
            <input type="text" name="username" required>
            <br>
            <label for="password">Password:</label>
            <input type="password" name="password" required>
            <input type="submit" value="submit-account-data">
        </form>
    </div>
</body>
</html>