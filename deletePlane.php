<?php
    require("ceckUser.php");
    require ("SQLconnect.php");
    $result = $conn->query("SELECT callsign FROM planes");
    while ($callsign = $result->fetch_array()){
        if(isset($_REQUEST["delete-".$callsign[0]])){
            $stmt = $conn->prepare('DELETE FROM planes WHERE callsign= ? ;');
            $stmt->bind_param("s",$callsign[0]);
            $stmt->execute();
            break;
        }
    }
    mysqli_close($conn);
    Header("Location:ATCplaneList.php");
?>