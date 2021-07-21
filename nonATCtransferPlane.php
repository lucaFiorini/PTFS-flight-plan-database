<?PHP
require ("SQLconnect.php");
require("functions.php");

$SQL='SELECT airport FROM airports';
$res = $conn->query($SQL);
$airportList = Array();
while($airport = $res->fetch_array()){
    array_push($airportList, $airport[0]);
}

$hashed_ip = hash('sha256',$_SERVER['REMOTE_ADDR']);
$result = $conn->query('SELECT callsign FROM planes WHERE created_by = "'.$hashed_ip.'"');
if($callsign = $result->fetch_array()){

    if(in_array($_REQUEST['nonATCplaneTransfer'],$airportList)){
        refreshInactivityTimer($callsign[0]);
        $stmt = $conn->prepare('UPDATE planes SET current_control_zone = ? WHERE callsign = ?' );
        $stmt -> bind_param("ss",$_REQUEST['nonATCplaneTransfer'],$callsign[0]);
        $stmt ->execute();
    }
    else echo('<script>alert("Invalid airport, please select an airport from the dropdown list");</script>');

}
mysqli_close($conn);
echo('<script>window.location.replace("nonATCplaneInterface.php")</script>');
exit();
?>