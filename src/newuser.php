<?php
require_once __DIR__ . "/classes/DB.php";
$db = DB::instance();
$queryAddUser = 'insert into Users(username,password) 
                 values (:username,:password)';
$stmt = $db->prepare($queryAddUser);
$username = $_POST['username'];
$password = password_hash($_POST['password']);
if ($stmt->execute(['username'=>$username, 'password'=>$password])){
    $_SESSION['username'] = $username;
    header("location: web/balance.php");
} else {
    header("location: web/registration.php");
}