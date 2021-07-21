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
<body>

    <center>
        <table border=1 width="700px">
            <tr>
                <td align="center" colspan="5">
                    <input type="checkbox" onclick="toggleAutoRefresh(this);" id="reloadCB"> Auto Refresh (NOTE: input fields may act up when auto-reload is active)
                </td>
            </tr>
            <tr>
                <td  colspan="5">
                    <form action="newPlane.php" method="post" id="newPlaneForm">
                        <div style="margin-top:2px">
                            <label style="width:15%; padding-right:0px; padding-left:0px; display:inline-block" for="callsign">Callsign*:</label>
                            <input style="width:30%; padding-right:0px; padding-left:0px; display:inline-block" type="text" name="callsign" pattern="^[a-z,A-Z]+-[0-9]{4,4}$" autocomplete="off" rquired>

                            <label style="width:20%; padding-right:0px; padding-left:0px; display:inline-block" for="aircraft">Aircraft*:</label>
                            <input style="width:32%; padding-right:0px; padding-left:0px; display:inline-block" type="text" name="plane" required>
                        </div>
                        <div style="margin-top:2px">
                            <label style="width:15%; padding-right:0px; padding-left:0px;  display:inline-block" for="clearance">Clearance:</label>
                            <input style="width:30%; padding-right:0px; padding-left:0px;  display:inline-block" type="text" name="clearance">
                            
                            <label style="width:20%; padding-right:0px; padding-left:0px;  display:inline-block" for="destiantion">Destination*:</label>
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
                                $conn->close();
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
                require("SQLconnect.php");
                $hashedIP = hash("sha256",$_SERVER['REMOTE_ADDR']);
                $stmt = $conn->prepare('SELECT selected_control_zone FROM atc_login_data WHERE hashedIP = ?');
                $stmt -> bind_param("s", $hashedIP);
                $stmt->execute();
                $res = $stmt->get_result();
                $selected_control_zone = $res -> fetch_array()[0];

                $stmt = $conn->prepare('SELECT * FROM planes WHERE current_control_zone = ?');
                $stmt-> bind_param("s",$selected_control_zone);
                $stmt->execute();
                $result = $stmt->get_result();
                    while ($row  = $result->fetch_object()) {
                        echo('
                        <tr>
                            <tr>
                                <td width="20%"');
                                    if ($row->emergency_status)
                                        echo(' style="color:red;"');
                                    echo ('>'. $row->callsign .'</td>'.'
                                    <td width="33%" rowspan="2" align="center">
                                        <form method="post" action="updateClearance.php" align="center">
                                            <input type="text" name="clearance-'.$row->callsign.'" value="'.$row->clearance.'" placeholder="clearance" style="width:95%; display: inline-block;">
                                            <input type="submit" value="update" style="width:100%">
                                        </form>
                                    </td>
                                    <td width="25%">
                                        DEPT: '.$row->origin.'
                                    </td>
                                    <td width="18%" rowspan="2">
                                        <form method="post" align="center" action="transferPlane.php">
                                            <select name="transfer-'.$row->callsign.'">
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
                                            $conn->close();
                                            echo ('</select>
                                            <input type="submit" value="transfer">
                                        </form>
                                    </td>
                                    <td rowspan="2" width="7%" align="center">
                                    <form method="post" action="deletePlane.php">
                                        <input type="hidden" value="true" name="delete-'.$row->callsign.'">
                                        <input type="submit" style="color:red;" value="delete">
                                    </form>
                                    </td>
                                </tr>
                            <td>
                                '.$row->aircraft.'
                            </td>
                            <td>
                                ARRV:'.$row->destination.'
                            </td>
                        </tr>'
                        );
                    }
                ?>
            </table>
        </center>
    </body>
</html>