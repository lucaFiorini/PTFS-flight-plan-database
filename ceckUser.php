<?php
{
    require ("SQLconnect.php");
    $hashedIP = hash("sha256",$_SERVER['REMOTE_ADDR']);
    $res = $conn->query('SELECT * FROM atc_login_data WHERE hashedIP = "'.$hashedIP.'"');
    if($res->num_rows == 0){
        echo('<script>alert("Invalid session")</script>');
        echo('<script>window.location.replace("index.php")</script>');
        exit();
    }
    else {
        $loginData = $res->fetch_object();
        if(time() - $loginData->loginTime > 10800){
            echo (time() - $loginData->loginTime);
            echo('<script>alert("Session Timed out")</script>');
            echo('<script>window.location.replace("index.php")</script>');
            exit();
        }
    }
    $conn->close();
}
?>