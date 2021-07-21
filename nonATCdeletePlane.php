<?php
    require ("SQLconnect.php");
    $hashed_ip = hash('sha256',$_SERVER['REMOTE_ADDR']);
    $result = $conn->query('DELETE FROM planes WHERE created_by = "'.$hashed_ip.'"');
    mysqli_close($conn);
    Header("Location:nonATCplaneInterface.php");
?>
