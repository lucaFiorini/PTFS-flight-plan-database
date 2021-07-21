<?php
function deleteInactivePlanes(){
    require("SQLconnect.php");
    $timeoutTime=10800;
    $timeToCeck=time() - $timeoutTime;
    $res=$conn->query('SELECT * FROM planes WHERE last_time_edited < '.$timeToCeck);

    while($row = $res->fetch_object()){
        $stmt = $conn->prepare("DELETE FROM planes WHERE callsign = ?");
        $stmt->bind_param("s",$row->callsign);
        $stmt->execute();
    }

}

function refreshInactivityTimer($callsign){

    require("SQLconnect.php");
    $stmt = $conn->prepare("UPDATE planes SET last_time_edited = UNIX_TIMESTAMP() WHERE callsign = ?");
    $stmt->bind_param("s",$callsign);
    $stmt->execute();
    print(mysqli_error($conn));

}
?>