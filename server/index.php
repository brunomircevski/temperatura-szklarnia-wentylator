<?php

require_once("config.php");

$current_temp = explode(",",file_get_contents('http://10.0.0.180'));

$current_temp[0] -= 100; $current_temp[1] -= 100; $current_temp[2] -= 100;

$error = false;

if(!($current_temp[0] > -30 && $current_temp[0] < 60 && $current_temp[1] > -30 && $current_temp[1] < 60 && $current_temp[2] > -30 && $current_temp[2] < 60)) {
    $error = true;
}

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if(isset($_GET['d'])) $d = $_GET['d'];
else $d = 0;

$records = array();
$time_from_fan_change = "";
    
if ($conn->connect_error) {
    echo("Błąd bazy danych: " . $conn->connect_error);

} else {
    $sql = 'SELECT greenhouse, indoor, outdoor, fan, DATE_FORMAT(date, "%H:%i") time FROM greenhouse WHERE date > DATE_ADD(DATE(NOW()), INTERVAL -'. $d .' DAY) AND date < DATE_ADD(DATE(NOW()), INTERVAL -'. --$d .' DAY)';

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $records[] = array($row["time"], $row["greenhouse"], $row["indoor"], $row["outdoor"], $row["fan"]);
        }
    }

    $sql = 'SELECT DATE_FORMAT(date, "%H:%i") time FROM greenhouse WHERE fan !='. $current_temp[3] .' AND date > DATE(NOW()) ORDER BY id DESC LIMIT 1';

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if($row) $time_from_fan_change = $row["time"];
    }
}

$conn->close();


?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Temperatura</title>
</head>
<body>
    <?php if($error) echo "<h2 class='error'>Wystąpił błąd w odczycie aktualnej temperatury</h2>";?>
    <section class="current-temps">
        <h2>Aktualna temperatura</h1>
        <div class="temp-card">
            <h5>Szklarnia</h5>
            <span><?php echo $current_temp[1]; ?></span>
        </div><div class="temp-card">
            <h5>Dom</h5>
            <span><?php echo $current_temp[0]; ?></span>
        </div><div class="temp-card">
            <h5>Zewnętrzna</h5>
            <span><?php echo $current_temp[2]; ?></span>
        </div>
        <div class="fan-info">Wentylator: <?php if($current_temp[3]==="1") echo "<span style='font-weight: 700; color:orange'>ON</span> "; else echo "<span style='font-weight: 700; color:blue'>OFF</span> "; ?><br><span class="date-fan-change"><?php echo $time_from_fan_change; ?></span></div>
    </section>

    <section>
        <h2>Wykres</h1>
        <canvas id="chart" class="chart" height="450"></canvas>
    </section>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.1/chart.min.js" integrity="sha512-QSkVNOCYLtj73J4hbmVoOV6KVZuMluZlioC+trLpewV8qMjsWqlIQvkn1KGX2StWvPMdWGBqim1xlC8krl1EKQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>const records = <?php echo(json_encode($records)); ?>;</script>
    <script src="script.js"></script>
</body>
</html>