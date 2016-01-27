<?php
    session_start();
    require 'database.php';

    
    if ($_SESSION['loginstatus']!=1){
        die;
    }
    
    $username=$_SESSION['username'];
    $viewer='%'.$_SESSION['username'];
    $viewer=$viewer.'%';
    $number=$_POST['month']+1;//the month number starts with 0 (January), so for display we need to add 1
    $month='%/'.$number;
    $month=$month.'/%';

    $stmt = $mysqli ->prepare("select date, importance from events where date like ? and (creator=? or viewer like ?)");    
    if (!$stmt){
        $error = $mysqli->error;
        $string="Query Prep Failed:" . $error; 
        echo json_encode(array(
            "message"=> $string));
        exit;
    }
    $stmt -> bind_param('sss',$month,$username,$viewer);
    $stmt -> execute();
    $stmt ->bind_result($date,$importance);
    $count = 0;
    $i=0;
    while ($stmt->fetch()){
        $count += 1;
        $dates[]=array("date"=>$date, "importance"=>$importance);
    }
    echo json_encode($dates);
    exit;
    if ($count == 0){
        echo json_encode(array("date"=>"not set"));
    }
    $stmt->close();
    header("Content-Type: application/json");
?>
