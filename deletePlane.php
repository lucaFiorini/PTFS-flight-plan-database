<?php
    require("ceckUser.php");
    require ("SQLconnect.php");
    $result = $conn->query("SELECT callsign FROM planes");
    while ($callsign = $result->fetch_array()){
        if(isset($_REQUEST["delete-".$callsign[0]])){
            $conn->query('DELETE FROM planes WHERE callsign="'.$callsign[0].'";');
            break;
        }
    }
    mysqli_close($conn);
    Header("Location:ATCplaneList.php");
?>