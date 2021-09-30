<!DOCTYPE html>
<?php
    require("ceckUser.php");
    require("functions.php");
    deleteInactivePlanes();
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css" type="text/css">
    <title>ATC helper</title>
    <script>
        var reloading;

        function checkReloading() {
            if (window.location.hash=="#autoreload") {
                reloading=setTimeout("window.location.reload();", 5000);
                document.getElementById("reloadCB").checked=true;
            }
        }

        function toggleAutoRefresh(cb) {
            if (cb.checked) {
                window.location.replace("#autoreload");
                reloading=setTimeout("window.location.reload();", 5000);
            } else {
                window.location.replace("#");
                clearTimeout(reloading);
            }
        }

        window.onload=checkReloading;
    </script>
</head>
<?php
require("SQLconnect.php");
$hashedIP = hash("sha256",$_SERVER['REMOTE_ADDR']);
$stmt = $conn->prepare('SELECT selected_control_zone FROM atc_login_data WHERE hashedIP = ?');
$stmt -> bind_param("s", $hashedIP);
$stmt->execute();
$res = $stmt->get_result();
$selected_control_zone = $res -> fetch_array()[0];
?>
<body>
    <center>
        <table width="700px">
            <tr>
                <td colspan="4" align="center">Current control zone: <b><?php echo($selected_control_zone)?></b></td>
            </tr>  
            <tr>
                <td align="center" colspan="4">
                    <input type="checkbox" onclick="toggleAutoRefresh(this);" id="reloadCB"> Auto Refresh (NOTE: input fields may act up when auto-reload is active)
                </td>
            </tr>
            <tr>
                <td  colspan="4">
                    <form action="newPlane.php" method="post" id="newPlaneForm">
                        <div style="margin-top:2px">
                            <label style="width:15%;" for="callsign">Callsign*:</label>
                            <input style="width:30%;" type="text" name="callsign" pattern="^[a-z,A-Z]+-[0-9]{4,4}$" autocomplete="off" rquired>

                            <label style="width:20%;" for="aircraft">Aircraft*:</label>
                            <input style="width:32%;" type="text" name="plane" required>
                        </div>
                        <div style="margin-top:2px">
                            <label style="width:15%;" for="clearance">Clearance:</label>
                            <input style="width:30%;" type="text" name="clearance">
                            
                            <label style="width:20%;" for="destiantion">Destination*:</label>
                            <select style="width:32.6%;" name="destination" required>
                                <option value="--select destination--">--select destination--</option>
                                <?php
                                $SQL='SELECT airport FROM airports;';
                                $res = $conn->query($SQL);
                                $airportList = Array();
                                while($airport = $res->fetch_array()){
                                    array_push($airportList, $airport[0]);
                                }
                                foreach($airportList as $airport){
                                    echo ('<option value="'.$airport.'">'.$airport.'</option>');
                                }

                                ?>
                            </select>
                        </div>      
                        <div style="margin-top:2px">
                            <input style="width:100%" type="submit" value="Submit new plane">
                        </div>
                    </form>
                </td>
            </tr>
            <?PHP

                $stmt = $conn->prepare('SELECT * FROM planes WHERE current_control_zone = ?');
                $stmt-> bind_param("s",$selected_control_zone);
                $stmt->execute();
                $result = $stmt->get_result();
                    while ($row  = $result->fetch_object()) {
                        echo('
                        <tr>
                            <td width="20%"');
                                if ($row->emergency_status)
                                    echo(' style="color:red;"');
                                echo ('> callsign: <b>'. $row->callsign .'</b></td>'.'
                            <td width="33%" align="center">
                                request: <b>'.$row->request.'<b>
                            </td>

                            <td width="20%" >
                                <form method="post" align="center" action="transferPlane.php">
                                    <select name="transfer-'.$row->callsign.'" style="width:60%">
                                    <option value="--transfer to--">--transfer to--</option>');
                                    require("SQLconnect.php");
                                    $stmt = $conn->prepare('SELECT airport FROM airports WHERE airport <> ? ;');
                                    $stmt->bind_param("s",$selected_control_zone);
                                    $stmt->execute();
                                    $res = $stmt->get_result();
                                    $airportList = Array();
                                    while($airport = $res->fetch_array()){
                                        array_push($airportList, $airport[0]);
                                    }
                                    foreach($airportList as $airport){
                                        echo ('<option value="'.$airport.'">'.$airport.'</option>');
                                    }
                                    $conn->close();?>
                                    </select>
                                    <input type="submit" value="transfer" style="width:35%">
                                </form>
                            </td>
                            <td rowspan="3" width="5%" align="center">
                                <form method="post" action="deletePlane.php">
                                    <input type="hidden" value="true" name="delete-<?php echo($row->callsign) ?>'">
                                    <input type="submit" style="color:red;" value="delete">
                                </form>
                            </td>
                        </tr>
                        <tr>
                            <td rowspan="2">
                                aircraft: <b><?php echo($row->aircraft) ?></b>
                            </td>
                            <td rowspan="2">
                                <form method="post" action="updateClearance.php" align="center">
                                    <input type="text" name="clearance-<?php echo($row->callsign) ?>" value="<?php echo ($row->clearance) ?>" placeholder="clearance" style="width:95%; display: inline-block;">
                                    <input type="submit" value="update clearace" style="width:100%">
                                </form>
                            </td>
                            <td>
                                <form action="updateOrigin.php" method="post"> 
                                    <label for="change-origin-<?php echo $row->callsign ?>" style="width:26%">DEPT:</label>
                                    <select name="change-origin-<?php echo $row->callsign ?>" style="width:70%">
                                        <option value="<?php echo ($row->origin)?>"><?php echo ($row->origin)?></option>
                                        <?php generateOptionList("airports","airport","airport",$row->origin) ?>
                                    </select>
                                    <input type="submit" value="update" style="width:100%"></input>
                                </form>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <form action="updateDestination.php" method="post" id="test"> 
                                <label for="change-destination-<?php echo $row->callsign ?>" style="width:26%">ARRV:</label>
                                    <select name="change-destination-<?php echo $row->callsign ?>" style="width:70%" onchange="this.form.submit()" >
                                        <option value="<?php echo ($row->destination)?>"><?php echo ($row->destination)?></option>
                                        <?php generateOptionList("airports","airport","airport",$row->destination) ?>
                                    </select>
                                </form>
                            </td>
                        </tr>
                    <?php } ?>
            </table>
        </center>
    </body>
</html>