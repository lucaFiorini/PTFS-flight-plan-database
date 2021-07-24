<?php
    require("SQLconnect.php");
    include("functions.php");

    $hashed_ip = hash('sha256',$_SERVER['REMOTE_ADDR']);

    $res = $conn->query('SELECT callsign FROM planes WHERE created_by = "'.$hashed_ip.'"');
    $callsign = $res->fetch_array()[0];
    refreshInactivityTimer($callsign);

    $request = $_REQUEST['request'];
    $stmt = $conn->prepare("UPDATE planes SET request = ? WHERE created_by = ?");
    $stmt ->bind_param("ss",$request,$hashed_ip);
    $stmt -> execute();
    
?>
<script>window.location.replace("nonATCplaneInterface.php")</script>
<?php exit();?>