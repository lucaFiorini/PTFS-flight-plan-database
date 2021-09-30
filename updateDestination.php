<?php
    
    require("ceckUser.php");
    require("SQLconnect.php");
    require("functions.php");
    $result = $conn->query("SELECT callsign FROM planes");
    while ($callsign = $result->fetch_array()){
        if(isset($_REQUEST["change-destination-".$callsign[0]])){
            echo "pippo";
            refreshInactivityTimer($callsign[0]);
            $destination = $_REQUEST["change-destination-".$callsign[0]];
            echo $destination;
            $stmt = $conn->prepare('UPDATE planes SET destination = ? WHERE callsign = ? ;');
            $stmt->bind_param("ss",$destination,$callsign[0]);
            $res = $stmt->execute();
            echo("found");
            break;
        }
        echo("test");
    }
    mysqli_close($conn);
    Header("Location:ATCplaneList.php");

?>