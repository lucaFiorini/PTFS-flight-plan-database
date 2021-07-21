<?PHP 
    require("ceckUser.php");
    require ("SQLconnect.php");
    require("functions.php");

    $SQL='SELECT airport FROM airports';
    $res = $conn->query($SQL);
    $airportList = Array();
    while($airport = $res->fetch_array()){
        array_push($airportList, $airport[0]);
    }

    $result = $conn->query("SELECT callsign FROM planes");

    while ($callsign = $result->fetch_array()){
        if(isset($_REQUEST["transfer-".$callsign[0]])){
            if(in_array($_REQUEST['transfer-'.$callsign[0]],$airportList)){
                refreshInactivityTimer($callsign[0]);
                $stmt = $conn->prepare('UPDATE planes SET current_control_zone = ? WHERE callsign = ?' );
                $stmt -> bind_param("ss",$_REQUEST['transfer-'.$callsign[0]],$callsign[0]);
                $stmt ->execute();
                break;
            }
            echo('<script>alert("Invalid airport, please select an airport from the dropdown list");</script>');
            break;
        }
    }
    mysqli_close($conn);
    echo('<script>window.location.replace("ATCplaneList.php")</script>');
    exit();
?>