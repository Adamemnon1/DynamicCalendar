<?php
    session_start();
    require 'database.php';
    
    if ($_SESSION['loginstatus']!=1){
        die;
    }
    
    $username=$_SESSION['username'];
    $date=$_POST['dateChosen'];
    $viewer='%'.$_SESSION['username'];
    $viewer=$viewer.'%';
    //in order find the events the viewer has access to we will use the like statement which is of the format  '%viewername%'
    //we preapere the right format here


    $stmt = $mysqli ->prepare("select ID, title, time from events where date=? and (creator=? or viewer like ?)");    
    if (!$stmt){
        $error = $mysqli->error;
        $string="Query Prep Failed:" . $error; 
        echo json_encode(array(
            "message"=> $string));
        exit;
    }
    $stmt -> bind_param('sss',$date,$username,$viewer);
    $stmt -> execute();
    $stmt ->bind_result($ID, $title,$time);
    $count = 0;
    while ($stmt->fetch()){
        $count += 1;
        $details[]=array("id"=>htmlentities($ID),"title"=> htmlentities($title), "time"=>htmlentities($time));
    }
    echo json_encode($details);
    $stmt->close();
    header("Content-Type: application/json");
?>
