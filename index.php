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
        <form method="post" action="performLogin.php">
            <label for="username">Username:</label>
            <input type="text" name="username" required>
            <br>
            <label for="password">Password:</label>
            <input type="password" name="password" required>
            <br>
            <select name="selected_control_zone" required>
                <option value="--select airport--">--select control zone--</option>
                <?php
                include("SQLconnect.php");
                $SQL='SELECT airport FROM airports;';
                $res = $conn->query($SQL);
                $airportList = Array();
                while($airport = $res->fetch_array()){
                    array_push($airportList, $airport[0]);
                }
                foreach($airportList as $airport){
                    echo ('<option value="'.$airport.'">'.$airport.'</option>');
                }
                $conn->close();
                ?>
            </select>
            <br>
            <input type="submit" value="logn">
        </form>
    </div>
</body>
</html>