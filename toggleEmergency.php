<?PHP
    require("SQLconnect.php");
    $hashedIP = hash('sha256',$_SERVER['REMOTE_ADDR']);
    $stmt = $conn->prepare('SELECT emergency_status FROM planes WHERE created_by = ?');
    $stmt -> bind_param("s",$hashedIP);
    $stmt -> execute();
    $res = $stmt -> get_result();
    $emergency_status = $res->fetch_array();
    $emergency = !($emergency_status["emergency_status"]);
    echo $emergency;
    $stmt = $conn->prepare('UPDATE planes SET emergency_status = ? WHERE created_by = ?');
    $stmt -> bind_param("is",$emergency,$hashedIP);
    $stmt -> execute();
    echo(mysqli_error($conn));
    echo('<script>window.location.replace("NonATCplaneInterface.php")</script>');
?>