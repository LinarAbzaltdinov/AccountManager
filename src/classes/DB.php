<?php
class DB
{
    private static $db = null;
    private static function create($dbsystem, $host, $db, $charset, $user, $password)
    {
        $dsn = "$dbsystem:host=$host;dbname=$db;charset=$charset";
        $opt = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false
        ];
        self::$db = new PDO($dsn, $user, $password, $opt);
    }
    public static function instance()
    {
        if (!isset(self::$db)) {
            $dbcfg = require __DIR__ . "/../DBconfig.php";
            self::create($dbcfg["dbsystem"], $dbcfg["host"], $dbcfg["name"], $dbcfg["charset"], $dbcfg["user"], $dbcfg["password"]);
        }
        return self::$db;
    }
}