<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "PTFS data";

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    echo("Connection failed: there was an error when attempting to connect to the database");
    }
?>