<?php 
    $dbname = 'mysql:host=localhost;dbname=banksystem';
    $dbuser = 'root';
    $dbpass = '';

    $conn = new PDO($dbname, $dbuser, $dbpass);
    if (!$conn){
        echo 'Not connected to database';
    }// else{
    //     echo 'Connected to database';
    // }
?>