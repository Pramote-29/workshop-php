<?php 
    session_start();
    require 'config.php';
    $minLength = 6;
    
    if(isset($_POST['register'])){
        $username = $_POST['username'];
        $password = $_POST['password'];
        $email = $_POST['email'];
        $Confrimpassword = $_POST['confirm_password'];
    }

   if(empty($username)){
    $_SESSION ['error'] = 'Please enter your username';
    header('location: register.php');
   } else if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
    $_SESSION ['error'] = 'Please enter a valid email';
    header('location: register.php');
   }else if(strlen($password) < $minLength){
    $_SESSION ['error'] ='Please enter a valid password';
    header('location: register.php');
   }else if($password !== $Confrimpassword){
    $_SESSION ['error'] ='Your password do not match';
    header('location: register.php');
   }else{
    $checkusername = $pdo -> prepare('SELECT COUNT(*)FROM users WHERE username = ?');
    $checkusername -> execute([$username]);
    $usernameExists = $checkusername ->fetchColumn();

    $checkemail = $pdo -> prepare('SELECT COUNT(*)FROM users WHERE email = ?');
    $checkemail -> execute([$email]);
    $useremailExists = $checkemail ->fetchColumn();

    if($usernameExists ){
        $_SESSION ['error'] ='Username already exists';
        header('location: register.php');
    }else if($useremailExists ){
        $_SESSION ['error'] ='Email already exists';
        header('location: register.php');
    }else{
        $hashpassword = password_hash($password, PASSWORD_DEFAULT);


        try{
            $smtm = $pdo -> prepare('INSERT INTO users(username, email, password) VALUES(?, ?, ?)');
            $smtm->execute([$username, $email, $hashpassword]);

            $_SESSION ['success'] ='Register Successfully';
            header('location: register.php');
        }catch(PDOException $e){
            $_SESSION ['error'] ='Somthing went wrong, please try again';
            echo"Register Failed" .$e -> getMessage();
            header('location: register.php');
        }
    }
   }
?>