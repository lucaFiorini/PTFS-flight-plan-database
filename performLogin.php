<?php

    include("SQLconnect.php");  
    $stmt = $conn->prepare('SELECT * FROM atc_login_data WHERE username = ?;');
    $stmt->bind_param("s",$_REQUEST['username']);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res -> num_rows == 0){
        echo('<script>alert("Invalid username");</script>');
        echo('<script>window.location.replace("index.php")</script>');
        exit();
    }

    else{
        $hashedPass = hash("sha256",$_REQUEST['password']);
        $user = $res->fetch_object();
        if ($user->hashed_password == $hashedPass){

            $SQL='SELECT airport FROM airports';
            $res = $conn->query($SQL);
            $airportList = Array();
            while($airport = $res->fetch_array())
                array_push($airportList, $airport[0]);
            
            if (in_array($_REQUEST['selected_control_zone'],$airportList)){
                
                $selected_control_zone = $_REQUEST['selected_control_zone'];
                
                $hashedIP = hash("sha256",$_SERVER['REMOTE_ADDR']);
                $stmt = $conn->prepare('UPDATE atc_login_data SET hashedIP = "N/A" WHERE hashedIP = ?'); //makes sure that there is only 1 account logged in with on a specific IP address
                $stmt->bind_param("s",$hashedIP);
                $stmt->execute();
                $stmt = $conn->prepare('UPDATE atc_login_data SET loginTime = UNIX_TIMESTAMP() , hashedIP=? , selected_control_zone =? WHERE hashed_password =?;');
                echo(mysqli_error($conn));
                $stmt->bind_param("sss",$hashedIP,$selected_control_zone,$hashedPass);
                $stmt->execute();

                echo('<script>alert("logged in");</script>');
                echo('<script>window.location.replace("ATCplaneList.php");</script>');
                exit();
            }
            else {
                echo('<script>alert("please select a conttrol zone");</script>');
                echo('<script>window.location.replace("index.php");</script>');
                exit();
            }

        }
        echo('<script>alert("wrong password");</script>');
        echo('<script>window.location.replace("index.php")</script>');
        exit();
    }

?>