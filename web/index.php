<?php
if (isset($_SESSION['username'])) {
    header("location: balance.php");
} else {
    header("location: login.php");
}