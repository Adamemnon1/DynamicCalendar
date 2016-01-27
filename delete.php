<?php
    session_start();
    require 'database.php';
    
    $id=$_POST['id'];
    
    if ($_SESSION['loginstatus']!=1){
     die;
    }

    //the code below filters the user unput for id
    if(!preg_match('/^[\d]+$/',$id)){
        die;
    }


    //the token from the post form is compared to the seesion token
    if($_SESSION['token'] !== $_POST['token']){
        die("Request forgery detected");
    }

    $stmt = $mysqli ->prepare("select creator from events where ID=?");    
    if (!$stmt){
        $error = $mysqli->error;
        $string="Query Prep Failed:" . $error; 
        echo json_encode(array(
            "message"=> $string));
        exit;
    }
    $stmt -> bind_param('s', $id);
    $stmt -> execute();
    $stmt ->bind_result($creator);
    while ($stmt->fetch()){
        if ($creator!=$_SESSION['username']){//this line verifies that the creator is the one who is perfomring the deletion
            die;
        }
    }
    $stmt->close();
      
    $stmt = $mysqli->prepare("delete from events where ID=?");
    if (!$stmt){
        $error = $mysqli->error;
        $string="Query Prep Failed:" . $error; 
        echo json_encode(array(
            "message"=> $string));
        exit;
    }

    $stmt->bind_param('s',$id);
    $stmt->execute();
    $stmt->close();
    
    echo json_encode(array("message"=>"Event deleted"));
    header("Content-Type: application/json");
?>
