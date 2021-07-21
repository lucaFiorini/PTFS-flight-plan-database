<?php
    require("ceckUser.php");
    require("SQLconnect.php");
    require("functions.php");
    $result = $conn->query("SELECT callsign FROM planes");
    while ($callsign = $result->fetch_array()){
        if(isset($_REQUEST["clearance-".$callsign[0]])){
            refreshInactivityTimer($callsign[0]);
            $clearance = $_REQUEST["clearance-".$callsign[0]];
            $stmt = $conn->prepare('UPDATE planes SET clearance = ? WHERE callsign = ? ;');
            $stmt->bind_param("ss",$clearance,$callsign[0]);
            $stmt->execute();
            break;
        }
    }
    mysqli_close($conn);
    Header("Location:ATCplaneList.php");
?>