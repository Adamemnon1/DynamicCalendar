<?php
    session_start();
    require 'database.php';
    //the code below exits the php when the user is not logged in 
    if ($_SESSION['loginstatus']!=1){
        die("Not logged in");
    }

    $username=$_SESSION['username'];
    $time=$_POST['time'];
    $title=$_POST['title'];
    $dateEvent=$_POST['dateEvent'];
    $importance=$_POST['importance'];
    $viewer=$_POST['viewer'];

    //the code below filters the input for the title
    if(!preg_match('/^([\w_\-]+\s*[\w_\-]+)*$/',$title)){
        die;
    }
    
    //the code below filters the input for the viewer
    if(!preg_match('/^([\w_\-]+,*[\w_\-]+)*$/',$viewer)){
        die;
    }

    //The database is accessed in a way that prevents sql injection attacks 
    $stmt = $mysqli->prepare("insert into events (title, date, time, creator,importance,viewer) values (?,?,?,?,?,?)");
     if (!$stmt){
        $error = $mysqli->error;
        $string="Query Prep Failed:" . $error; 
        echo json_encode(array(
            "message"=> $string));//if there is an error, the error message becomes the output
        exit;
    }
    $stmt->bind_param('ssssss',$title,$dateEvent,$time,$username,$importance,$viewer);
    $stmt->execute();
    $stmt->close();
    echo json_encode(array("success"=>true, "message"=>"Event added!"));
    header("Content-Type: application/json");
?>
