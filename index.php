<?php
/**
 * Created by PhpStorm.
 * User: polzovatel
 * Date: 30/11/2017
 * Time: 02:38
 */
$dbcfg = require_once "./DBconfig.php";
$dsn = $dbcfg["dbsystem"] . ":host=" . $dbcfg["host"] . ";dbname=" . $dbcfg["name"] . ";charset=" . $dbcfg["charset"];
$DB = new PDO($dsn, $dbcfg["user"], $dbcfg["password"]);
echo $DB->query("SELECT * FROM Users")->columnCount();