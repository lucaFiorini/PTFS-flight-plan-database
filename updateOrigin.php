<?php 

    require("ceckUser.php");
    require("SQLconnect.php");
    require("functions.php");
    $result = $conn->query("SELECT callsign FROM planes");
    while ($callsign = $result->fetch_array()){
        if(isset($_REQUEST["change-origin-".$callsign[0]])){
            refreshInactivityTimer($callsign[0]);
            $destination = $_REQUEST["change-origin-".$callsign[0]];
            $stmt = $conn->prepare('UPDATE planes SET origin = ? WHERE callsign = ? ;');
            $stmt->bind_param("ss",$destination,$callsign[0]);
            $stmt->execute();
            break;
        }
    }
    mysqli_close($conn);
    Header("Location:ATCplaneList.php");

?>