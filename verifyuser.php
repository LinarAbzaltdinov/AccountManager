<?php
$db = DB::instance();
$username = $_POST['username'];
$password = $_POST['password'];
$queryFindUser = 'select password from Users 
                 where username = '.$username;
$stmt = $db->query($queryFindUser);
if (isset($stmt) && password_verify($password, $stmt->fetch())['password']){
    $_SESSION['username'] = $username;
    header("location: views/balance.php");
} else {
    header("location: views/login.php");
}