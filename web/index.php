<?php
if (isset($_SESSION['username'])) {
    header("location: lk.php");
} else {
    header("location: login.php");
}