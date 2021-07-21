<?php
    require("ceckUser.php");
    require ("SQLconnect.php");

    $SQL = "SELECT callsign FROM planes";
    $res = $conn->query($SQL);
    $callsignList = Array();
    while($callsign = $res->fetch_array())
        array_push($callsignList,$callsign[0]);
    
    $SQL='SELECT airport FROM airports';
    $res = $conn->query($SQL);
    $airportList = Array();
    while($airport = $res->fetch_array()){
        array_push($airportList, $airport[0]);
    }
    $hashedIP = hash("sha256",$_SERVER['REMOTE_ADDR']);
    $stmt = $conn->prepare('SELECT selected_control_zone FROM atc_login_data WHERE hashedIP = ?');
    $stmt->bind_param("s",$hashedIP);
    $stmt->execute();
    $res = $stmt->get_result();
    $selected_control_zone = $res -> fetch_array()[0];

    if(!in_array($_REQUEST['destination'],$airportList))
        echo('<script>alert("Invalid airport, please select an airport from the dropdown list")</script>');
    
    else if(in_array($_REQUEST['callsign'], $callsignList))
        echo('<script>alert("Another user is already using this callsign")</script>');

    else {
        $stmt = $conn->prepare('
        INSERT INTO planes (callsign, aircraft, notes, current_control_zone,origin,destination,last_time_edited) 
        VALUES ( ? , ? , ? , ? , ? , ? , UNIX_TIMESTAMP());
        ');
        $stmt->bind_param(
            "ssssss",
            $_REQUEST['callsign'],
            $_REQUEST['plane'],
            $_REQUEST['notes'],
            $selected_control_zone,
            $selected_control_zone,
            $_REQUEST['destination']
            );
        $stmt->execute();
        $res=$stmt->get_result();
    }

    mysqli_close($conn);
    echo('<script>window.location.replace("ATCplaneList.php")</script>');
    exit();
?>