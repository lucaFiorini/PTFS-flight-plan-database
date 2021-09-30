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

}

function generateOptionList($tbname,$tbcol,$conditionColumn = false ,$condition = false){

    require("SQLconnect.php");

    $SQLstring = 'SELECT '.$tbcol.' FROM '.$tbname;
    if ($conditionColumn and $condition)
        $SQLstring.=' WHERE '.$conditionColumn.' <> ?';
    
    $stmt = $conn->prepare($SQLstring);
    
    if ($conditionColumn and $condition)
        $stmt->bind_param("s",$condition);

    $stmt->execute();
    $res = $stmt->get_result();
    $airportList = Array();
    while($airport = $res->fetch_array()){
        array_push($airportList, $airport[0]);
    }
    foreach($airportList as $airport){
        echo ('<option value="'.$airport.'">'.$airport.'</option>');
    }
    $conn->close();
}
?>