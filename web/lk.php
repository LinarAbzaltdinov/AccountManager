<?php
session_start();
if (!isset($_SESSION['username']))
    header("location: index.php");
require_once __DIR__.'/../src/classes/Account.php';
require_once __DIR__.'/../src/classes/User.php';
require_once __DIR__.'/../src/classes/DB.php';
require_once __DIR__.'/../src/DBconfig.php';
require_once __DIR__.'/../src/classes/ExchangeRates.php';
require_once __DIR__.'/../src/classes/Currency.php';

require_once __DIR__.'/../vendor/autoload.php';


$menubar = require_once __DIR__.'/menu.php';
$username = $_SESSION['username'];
$pageTitle = 'Личный кабинет';
$activePage = 'account';
if (isset($_GET['active'])) {
    $pageTitle = $menubar[$_GET['active']];
    $activePage = $_GET['active'];
}
$rates = ExchangeRates::getRatesFromApi();
$twigElems = array(
    'pageTitle' => $pageTitle,
    'username' => $username,
    'menubar' => $menubar,
    'exchangeRates' => $rates
);

switch ($activePage) {
    case 'account':
        $user_id = $_SESSION['user_id'];
        $accounts = User::getAccountIDs($user_id);
        $accountBalances = array();
        foreach ($accounts as $acc) {
            $balance = Account::getBalance($acc);
            $accountBalances[$acc] = $balance;
        }
        $twigElems['history'] = $accountBalances;

        $formAcition = 'create';
        if (isset($_GET['formAction']))
            $formAcition = $_GET['formAction'];
        $twigElems['formAction'] = $formAcition;

        switch($formAcition) {
            case 'create':
                $twigElems['currencies'] = Currency::getCurrencies();
                break;
            case 'update':
                break;
        }

        break;
}
$loader = new Twig_Loader_Filesystem(__DIR__.'/templates');
$twig = new Twig_Environment($loader);
echo $twig->render($activePage.'.html', $twigElems);

//echo '<pre>'; print_r(Account::getBalance(1)); echo '</pre>';
