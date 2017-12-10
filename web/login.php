<?php
if (isset($_SESSION['username'])) {
    header("location:lk.php");
}

require_once __DIR__.'/../vendor/autoload.php';

$loader = new Twig_Loader_Filesystem(__DIR__.'/templates');
$twig = new Twig_Environment($loader);
$error = false;
if (isset($_GET['error']))
    $error = true;
echo $twig->render('login.html', array(
        'pageTitle' => "Вход в систему",
        'isRegistration' => false,
        'error' => $error
    ));