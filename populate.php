<?php
    session_start();
    require 'database.php';
    
    if ($_SESSION['loginstatus']!=1){
        die;
    }
    
    $id=$_POST['idChosen'];

    if(!preg_match('/^[\d]+$/',$id)){
        die;
    }
    
    $stmt = $mysqli ->prepare("select title, time, viewer, creator from events where ID=?");    
    if (!$stmt){
        $error = $mysqli->error;
        $string="Query Prep Failed:" . $error; 
        echo json_encode(array(
            "message"=> $string));
        exit;
    }
    $stmt -> bind_param('s', $id);
    $stmt -> execute();
    $stmt ->bind_result($title,$time, $viewer,$creator);
    while ($stmt->fetch()){
        if ($creator!=$_SESSION['username']){
            echo json_encode(array("message"=>"failure"));
            exit;
        }
        echo json_encode(array("title"=>htmlentities($title), "time"=>htmlentities($time), "viewer"=>htmlentities($viewer), "message"=>"success"));
    }
    $stmt->close();
    header("Content-Type: application/json");
?>
