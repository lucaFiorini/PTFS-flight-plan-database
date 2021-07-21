<?php
    require("ceckUser.php");
    require("SQLconnect.php");
    require("functions.php");
    $result = $conn->query("SELECT callsign FROM planes");
    while ($callsign = $result->fetch_array()){
        if(isset($_REQUEST["notes-".$callsign[0]])){
            refreshInactivityTimer($callsign[0]);
            $notes = $_REQUEST["notes-".$callsign[0]];
            $stmt = $conn->prepare('UPDATE planes SET notes = ? WHERE callsign = ? ;');
            $stmt->bind_param("ss",$notes,$callsign[0]);
            $stmt->execute();
            break;
        }
    }
    mysqli_close($conn);
    Header("Location:ATCplaneList.php");
?>