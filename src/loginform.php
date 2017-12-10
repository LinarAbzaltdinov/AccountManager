<?php
require_once __DIR__ . "/classes/DB.php";
require_once __DIR__ . "/classes/User.php";
switch ($_POST['action']) {
    case 'signin':
        $username = $_POST['username'];
        $password = $_POST['password'];
        $haveAccess = User::verify($username, $password);
        if ($haveAccess) {
            $_SESSION['username'] = $username;
            header("location: ../web/balance.php");
        } else
            header("location: ../web/login.php?error=true");
        break;
    case 'signup':
        $username = $_POST['username'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $isRegistered = User::create($username, $password);
        if ($isRegistered) {
            $_SESSION['username'] = $username;
            header("location: ../web/balance.php");
        } else
            header("location: ../web/registration.php?error=true");
        break;
    default:
        header("location: ../web/index.php");
}