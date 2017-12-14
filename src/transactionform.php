<?php
session_start();
require_once __DIR__.'/../src/classes/DB.php';
require_once __DIR__.'/../src/DBconfig.php';
require_once __DIR__.'/../src/classes/Currency.php';
require_once __DIR__.'/../src/classes/Account.php';
require_once __DIR__.'/../src/classes/Transaction.php';

$acc_curr_id_from = null;
$acc_curr_id_to = null;
$cat_id = isset($_POST['cat_id']) ? $_POST['cat_id'] : null;
$value = empty($_POST['value']) ? 0 : $_POST['value'];
$ex_rate = isset($_POST['ex_rate']) ? $_POST['ex_rate'] : null;

$action = $_POST['formAction'];
if ($action == 'outcome' || $action == 'transfer') {
    $db = DB::instance();
    $stmt = $db->prepare("SELECT id
                              FROM Account_Currency
                              WHERE acc_id = :acc_id
                              AND curr_id = :curr_id");
    $stmt->execute(['acc_id' => $_POST['acc_id_from'], 'curr_id' => $_POST['curr_id_from']]);
    $acc_curr_id_from = $stmt->fetch(PDO::FETCH_ASSOC)['id'];
}
if ($action == 'income' || $action == 'transfer') {
    $db = DB::instance();
    $stmt = $db->prepare("SELECT id
                              FROM Account_Currency
                              WHERE acc_id = :acc_id
                              AND curr_id = :curr_id");
    $stmt->execute(['acc_id' => $_POST['acc_id_to'], 'curr_id' => $_POST['curr_id_to']]);
    $acc_curr_id_to = $stmt->fetch(PDO::FETCH_ASSOC)['id'];
}
Transaction::AddTransaction($acc_curr_id_from, $acc_curr_id_to, $cat_id, $value, $ex_rate);
header("location: ../web/lk.php?active=$action");