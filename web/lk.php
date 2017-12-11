<?php
session_start();
require_once __DIR__.'/../src/classes/Account.php';
require_once __DIR__.'/../src/classes/DB.php';
require_once __DIR__.'/../src/DBconfig.php';
require_once __DIR__.'/../src/classes/ExchangeRates.php';

require_once __DIR__.'/../vendor/autoload.php';

$loader = new Twig_Loader_Filesystem(__DIR__.'/templates');
$twig = new Twig_Environment($loader);
$menubar = require_once __DIR__.'/menu.php';
$username = $_SESSION['username'];
$pageTitle = 'Личный кабинет';
$rates = ExchangeRates::getRatesFromApi();
if (isset($_GET['active'])) {
    $pageTitle = $menubar[$_GET['active']];
}
echo $twig->render('userpage.html', array(
    'pageTitle' => $pageTitle,
    'username' => $username,
    'menubar' => $menubar,
    'exchangeRates' => $rates
));

//echo '<pre>'; print_r(Account::getBalance(1)); echo '</pre>';
