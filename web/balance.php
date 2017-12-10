<?php
require_once __DIR__.'/../src/classes/Account.php';
require_once __DIR__.'/../src/classes/DB.php';
require_once __DIR__.'/../src/DBconfig.php';
echo '<pre>'; print_r(Account::getBalance(1)); echo '</pre>';