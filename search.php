<?php
    session_start();
    require 'database.php';
    
    if ($_SESSION['loginstatus']!=1){
        die;
    }
    
    if(!preg_match('/^([\w_\-]+\s*[\w_\-]+)*$/',$_POST['title'])){
        die;
    }

    $username=$_SESSION['username'];
    $title='%'.$_POST['title'];
    $title=$title.'%';


    
    $stmt = $mysqli ->prepare("select ID, title, time, date from events where title like ? and creator=?");    
    if (!$stmt){
        $error = $mysqli->error;
        $string="Query Prep Failed:" . $error; 
        echo json_encode(array(
            "message"=> $string));
        exit;
    }
    $stmt -> bind_param('ss',$title,$username);
    $stmt -> execute();
    $stmt ->bind_result($ID, $title,$time,$date);
    $count = 0;
    while ($stmt->fetch()){
        $count += 1;
        $details[]=array("id"=>htmlentities($ID),"title"=> htmlentities($title), "time"=>htmlentities($time), "date"=>htmlentities($date));
    }
    echo json_encode($details);
    $stmt->close();
    header("Content-Type: application/json");
?>
