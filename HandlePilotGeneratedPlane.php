<?php
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

    $conn->query('DELETE FROM planes WHERE created_by = "'.$hashedIP.'"');

    if(!in_array($_REQUEST['destination'],$airportList) or !in_array($_REQUEST['origin'],$airportList))
        echo('<script>alert("Invalid airport, please select an airport from the dropdown list")</script>');
    
    else if(in_array($_REQUEST['callsign'], $callsignList))
        echo('<script>alert("Another user is already using this callsign")</script>');

    else {
        $stmt = $conn->prepare('
        INSERT INTO planes (callsign, aircraft, current_control_zone,origin,destination,last_time_edited,created_by) 
        VALUES ( ? , ? , ? , ? , ? , UNIX_TIMESTAMP(), ? );
        ');
        $stmt->bind_param(
            "ssssss",
            $_REQUEST['callsign'],
            $_REQUEST['aircraft'],
            $_REQUEST['origin'],
            $_REQUEST['origin'],
            $_REQUEST['destination'],
            $hashedIP
            );
        $stmt->execute();
        $res=$stmt->get_result();
    }

    mysqli_close($conn);
    echo('<script>window.location.replace("nonATCplaneInterface.php")</script>');
    exit();
?>