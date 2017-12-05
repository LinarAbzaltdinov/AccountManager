<?php
require_once __DIR__ . "/classes/DB.php";
$db = DB::instance();
$username = $_POST['username'];
$password = $_POST['password'];
$queryFindUser = 'select password from Users 
                  where username = :username';
$stmt = $db->prepare($queryFindUser);
$res = $stmt->execute(['username'=>username]);
if ($res && password_verify($password, $res->fetch())['password']){
    $_SESSION['username'] = $username;
    header("location: web/balance.php");
} else {
    header("location: web/login.php");
}