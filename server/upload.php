<?php

require_once("config.php");

$current_temp = explode(",",file_get_contents('http://10.0.0.180'));

$current_temp[0] -= 100; $current_temp[1] -= 100; $current_temp[2] -= 100;

$error = false;

if(!($current_temp[0] > -30 && $current_temp[0] < 60 && $current_temp[1] > -30 && $current_temp[1] < 60 && $current_temp[2] > -30 && $current_temp[2] < 60)) {
    $error = true;
    echo "Bad data!";
}

if(!$error) {
    $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "INSERT INTO greenhouse VALUES (NULL, {$current_temp[1]}, {$current_temp[0]}, {$current_temp[2]}, NOW(), $current_temp[3])";
    
    if ($conn->query($sql) === TRUE) {
        echo "New record created successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
    
    $conn->close();
}

?>