<?php
$db = DB::instance();
$queryAddUser = 'insert into Users(username,password) 
                 values (:username,:password)';
$stmt = $db->prepare($queryAddUser);
$username = $_POST['username'];
$password = password_hash($_POST['password']);
if ($stmt->execute(['username'=>$username, 'password'=>$password])){
    $_SESSION['username'] = $username;
    header("location: views/balance.php");
} else {
    header("location: views/registration.php");
}