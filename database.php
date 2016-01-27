<?php
    $mysqli= new mysqli('localhost','user','123',"calendar");//signs in to the right database with a specifically created account
    if ($mysqli->connect_errno){
        printf("Connection Failed: %s\n", $mysqli->connect_error);
        exit;
    }
?>
