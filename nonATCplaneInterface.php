<!DOCTYPE html>
<html lang="en">
<head>
    <?php 
        require("functions.php");
        deleteInactivePlanes();
    ?>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style.css" type="text/css">
    <script>
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
<body>
    <center>
        <table border = "1" width="700px">
            <tr>
                <td align="center" colspan="4">
                    <input type="checkbox" onclick="toggleAutoRefresh(this);" id="reloadCB"> Auto Refresh (NOTE: input fields may act up when auto-reload is active)
                </td>
            </tr>
                <td colspan="4">
                <form action="HandlePilotGeneratedPlane.php">
                    <div style="margin-top:2px">
                            <label style="width:16%; padding-right:0px; padding-left:0px; display:inline-block" for="callsign">Callsign*:</label>
                            <input style="width:32%; padding-right:0px; padding-left:0px; display:inline-block" type="text" name="callsign" pattern="^[a-z,A-Z]+-[0-9]{4,4}$" autocomplete="off" rquired>
                            <label style="width:16%; padding-right:0px; padding-left:0px; display:inline-block" for="aircraft">Aircraft*:</label>
                            <input style="width:31%; padding-right:0px; padding-left:0px; display:inline-block" type="text" name="aircraft" required>
                        </div>

                        <div style="margin-top:2px">
                            <label style="width:16%; padding-right:0px; padding-left:0px; display:inline-block">Orign*:</label>
                            <select style="width:32.5%; padding-right:0px; padding-left:0px; display:inline-block" name="origin" required>
                                <option value="---select origin---">---select origin---</option>
                                <?php
                                require("SQLconnect.php");
                                $SQL='SELECT airport FROM airports;';
                                $res = $conn->query($SQL);
                                $airportList = Array();
                                while($airport = $res->fetch_array()){
                                    array_push($airportList, $airport[0]);
                                }
                                foreach($airportList as $airport){
                                    echo ('<option value="'.$airport.'">'.$airport.'</option>');
                                }
                                $conn->close();
                                ?>
                            </select>
                            
                            <label style="width:16%; padding-right:0px; padding-left:0px; display:inline-block">Destination*:</label>
                            <select style="width:32%; padding-right:0px; padding-left:0px; display:inline-block" name="destination" required>
                                <option value="--select destination--">--select destination--</option>
                                <?php
                                require("SQLconnect.php");
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
                        <div style="margin-top:3px">
                            <input style="width:100%; display:inline-block"type="submit" value="sumbit/replace plane">
                        </div>
                        </form>
                    </td>
                </tr>
                <?php
                $hashedIP = hash("sha256",$_SERVER['REMOTE_ADDR']);
                $stmt = $conn->prepare("SELECT * FROM planes WHERE created_by = ?");
                $stmt -> bind_param("s",$hashedIP);
                $stmt -> execute();
                $res = $stmt->get_result();
                if ($res -> num_rows > 0) {
                    $ownedPlane = $res->fetch_object();
                    ?>
                    <tr>
                        <td colspan="3">CURRENT CLEARANCE: <b><?php echo $ownedPlane->clearance; ?></b></td>
                    </tr>
                    <tr>
                        <td align="center"> Callsign: <b><?PHP echo $ownedPlane->callsign; ?></b></td>
                        <td> Aircraft: <b><?PHP echo $ownedPlane->aircraft ?></b></td>
                        <td align="center">
                            <form action="toggleEmergency.php" method="post">
                                <label for="emergency-toggle">Emergency status: </label><?php
                                if ($ownedPlane->emergency_status){
                                    ?><input type="submit" name="emergency-toggle" value="ON"><?PHP
                                }
                                else {
                                    ?><input type="submit" name="emergency-toggle" value="OFF"><?PHP
                                }
                        ?> </form>
                        </td>
                    </tr>
                    <tr>
                        <td rowspan="2" align="center" width="25%">Current control zone:<br><?php echo $ownedPlane->current_control_zone ?></td>
                        <td width="20%">DEPT: <?php echo $ownedPlane->origin ?></td>
                        <td width="30%" align="center">
                            <form action="NonATCtransferPlane.php" method="post">
                                <select style="width:70%; padding-right:0px; padding-left:0px; display:inline-block" name="nonATCplaneTransfer" required>
                                    <option width="100%" value="--transfer to--">---------transfer to---------</option>
                                    <?php
                                    require("SQLconnect.php");
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
                                <input type="submit" value="transfer" style="width:25%; padding-right:0px; padding-left:0px; display:inline-block" >
                            </form>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="1">ARRV: <?php echo $ownedPlane->destination ?></td>
                        <td align="center">
                            <form action="nonATCupdateRequest.php" method="post">
                                <input type="text" name="request" style="width:70%; padding-right:0px; padding-left:0px; display:inline-block" placeholder="request" value="<?php echo $ownedPlane->request?>">
                                <input type="submit" value="update" style="width:25%; padding-right:0px; padding-left:0px; display:inline-block">
                            </form>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" align="center">
                            <div style="width:100%">
                                <form method="post" action="nonATCdeletePlane.php">
                                    <input type="hidden" value="true" name="delete">
                                    <input type="submit" style="color:red; display:inline-block; width:100%" value="DELETE PERSONAL PLANE">
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php
                };
                ?>
        </table>
    </center>
</body>
</html>