<?php
session_start();
require_once __DIR__.'/../src/classes/DB.php';
require_once __DIR__.'/../src/DBconfig.php';
require_once __DIR__.'/../src/classes/Currency.php';
require_once __DIR__.'/../src/classes/Account.php';

$currencies = Currency::getCurrencies();
switch ($_POST['formAction']) {
    case 'create':
        $acc_name = $_POST['name'];
        $currencyValuesToCreate=array();
        foreach ($currencies as $currency) {
            if (isset($_POST[$currency['id']]) && $_POST[$currency['id']]=="on") {
                $tmpcurr = array();
                $tmpcurr['curr_id'] = $currency['id'];
                $tmpcurr['init_value'] = $_POST[$currency['id'].'value'];
                array_push($currencyValuesToCreate, $tmpcurr);
            }
        }
        Account::createAcc($_SESSION['user_id'], $acc_name, $currencyValuesToCreate);
        break;
    case 'update':
        $acc_name = $_POST['name'];
        $acc_name = $_POST['name'];
        $currencyValuesToCreate=array();
        foreach ($currencies as $currency) {
            if (isset($_POST[$currency['id']]) && $_POST[$currency['id']]=="on") {
                $tmpcurr = array();
                $tmpcurr['curr_id'] = $currency['id'];
                $tmpcurr['init_value'] = $_POST[$currency['id'].'value'];
                array_push($currencyValuesToCreate, $tmpcurr);
            }
        }
        Account::createAcc($_SESSION['user_id'], $acc_name, $currencyValuesToCreate);
        break;
        break;
}
header("location: ../web/lk.php?active=account");