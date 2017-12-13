<?php
session_start();
require_once __DIR__ . "/classes/DB.php";
require_once __DIR__ . "/classes/User.php";
switch ($_POST['action']) {
    case 'signin':
        $username = $_POST['username'];
        $password = $_POST['password'];
        $user_id = User::verify($username, $password);
        if ($user_id != false) {
            $_SESSION['username'] = $username;
            $_SESSION['user_id'] = $user_id;
            header("location: ../web/lk.php");
        } else
            header("location: ../web/login.php?error=true");
        break;
    case 'signup':
        $username = $_POST['username'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $user_id = User::create($username, $password);
        if ($user_id != false) {
            $_SESSION['username'] = $username;
            $_SESSION['user_id'] = $user_id;
            header("location: ../web/lk.php");
        } else
            header("location: ../web/registration.php?error=true");
        break;
    default:
        header("location: ../web/index.php");
}