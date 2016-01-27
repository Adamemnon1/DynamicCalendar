<?php
    session_start();
    require 'database.php'; 
    
    $username= $_POST['username'];
    $password1= $_POST['password1'];
    $password2=$_POST['password2'];
    
    if(!preg_match('/^[\w_\-]+$/',$username)){
        echo json_encode(array(
            "message"=>"Invalid username"));
        exit;
     }
     
    if(!preg_match('/^[a-zA-Z0-9]+$/',$password1)){
        echo json_encode(array(
            "message"=>"Invalid password"));
        exit;
     }
     
    if(!preg_match('/^[a-zA-Z0-9]+$/',$password2)){
        echo json_encode(array(
            "message"=>"Invalid password"));
        exit;
     }
    
    //if the passwords don't match, notift the user
    if ($password1 != $password2){
        echo json_encode(array(
            "message"=>"The passwords you entered did not match."));
        exit;
    }
    
    //tablename = users. username and password are the two columns.
    $stmt = $mysqli->prepare("select COUNT(*) from users where username=?");
    $stmt->bind_param('s', $username);
    $stmt->execute();
    
    $stmt->bind_result($cnt);
    $stmt->fetch();
    
    //if the username is alreay uesed, prompt the user to choose a different one
    if ($cnt==1) {
        echo json_encode(array(
            "message"=>"The username has already been used."));
        exit;
    }

    $stmt->close();
    
    $stmt = $mysqli->prepare("insert into users (username, password) values (?,?)");
    if (!$stmt){
        $error = $mysqli->error;
        $string="Query Prep Failed:" . $error; 
        echo json_encode(array(
            "message"=> $string));
        exit;
    }
    $stmt->bind_param('ss',$username,crypt ("$password1"));
    $stmt->execute();
    $stmt->close();

    $_SESSION['loginstatus']=1;
    $_SESSION['username']=$username;
    $_SESSION['token'] = substr(md5(rand()), 0, 10);//a token is generated randomly for use later to prevent csrf attacks
     echo json_encode(array(
            "message"=> "You have successfully registered!",
            "success"=>true,
            "username"=>$username
            )
    );
    header("Content-Type: application/json");
?>
