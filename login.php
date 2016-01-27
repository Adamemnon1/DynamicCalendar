<?php
    session_start();
    require 'database.php';
    
    $username= $_POST ['username'];
    $password=$_POST['password'];
    $_SESSION['token'] = substr(md5(rand()), 0, 10);//a token is created here randomly to prevent CSRF attack
    
    //the code below sanitizes the username input
    if(!preg_match('/^[\w_\-]+$/',$username)){
        echo json_encode(array(
            "message"=>"Invalid username"));
        exit;
     }
     
     //code below sanitized the password input
    if(!preg_match('/^[a-zA-Z0-9]+$/',$password)){
        echo json_encode(array(
            "message"=>"Invalid password"));
        exit;
     }
     
    //give back the number of matches with specified username
    $stmt = $mysqli->prepare("select COUNT(*), password from users where username=?");
    $stmt->bind_param('s', $username);
    $stmt->execute();
    
    $stmt->bind_result($cnt, $pw);
    $stmt->fetch();
    
    if ($cnt==0) {               
         echo json_encode(array(
            "message"=>"Account does not exist!"));
        exit;}

    //if there is one match, log the user in accordingly 
    if ($cnt ==1 && crypt($password,$pw)==$pw){
        $_SESSION['loginstatus'] =1;
        $_SESSION['username']=$username;
        echo json_encode(array(
            "message"=>"You have logged in!",
            "username"=>$username,
            "success"=>true
        ));
        exit;
    }else{
        echo json_encode(array(
            "message"=>"Incorrect password!",
        ));
        exit;
    }
    header("Content-Type: application/json");
?>
