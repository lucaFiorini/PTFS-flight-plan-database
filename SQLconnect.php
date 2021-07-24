<?php
$servername = "localhost";
$username = "XD";
$password = "XD";
$dbname = "yeet";

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    echo("Connection failed: " . mysqli_connect_error());
    }
?>
