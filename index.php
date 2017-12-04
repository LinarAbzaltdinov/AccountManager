<?php
//session_start();
 /*
if (isset($_SESSION['username'])) {
    header("location: views/balance.php");
} else {
    header("location: views/login.php");
}*/
 echo DB::instance()->query('Select * from Users')->columnCount();
