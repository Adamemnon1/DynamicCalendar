<?php
    session_start();
    require 'database.php';

    if ($_SESSION['loginstatus']!=1){
        die;
    }
    
    if($_SESSION['token'] !== $_POST['token']){
        die("Request forgery detected");
    }
    
    $title=$_POST['title'];
    $time=$_POST['time'];
    $id=$_POST['idChosen'];
    $viewer=$_POST['viewer'];

    if(!preg_match('/^([\w_\-]+\s*[\w_\-]+)*$/',$title)){
        die;
    }

    if(!preg_match('/^[\d]+$/',$id)){
        die;
    }

    if(!preg_match('/^([\w_\-]+,*[\w_\-]+)*$/',$viewer)){
        die;
    }

    
    $stmt = $mysqli->prepare("update events set title=?, time=?,viewer=?  where ID=?");
    if (!$stmt){
        $error = $mysqli->error;
        $string="Query Prep Failed:" . $error; 
        echo json_encode(array(
            "message"=> $string));
        exit;
    }
    $stmt->bind_param('ssss',$title,$time,$viewer,$id);
    $stmt->execute();
    $stmt->close();
    echo json_encode(array("message"=>"Successfully editted!"));
?>
