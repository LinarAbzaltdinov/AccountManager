<?php
/**
 * Created by PhpStorm.
 * User: linarkou
 * Date: 11/12/2017
 * Time: 14:47
 */

class ExchangeRates
{
    static private $dateOfLastUpdate;
    static private $rates;
    static public function getRatesFromApi() //all exchange rates to RUB
    {
        require_once "DB.php";
        $today = date("Y-m-d");
        $lastUpdateWeekday = date('w', self::$dateOfLastUpdate);
        if (!isset(self::$dateOfLastUpdate) ||
            $today != self::$dateOfLastUpdate &&
            !($lastUpdateWeekday == 5 && date_diff(self::$dateOfLastUpdate, $today) <= 2)) // don't update at weekend
        {
            $db = DB::instance();
            $res = $db->query("SELECT name FROM Currency")->fetchAll(PDO::FETCH_ASSOC);
            $dbCurrencies = array();
            foreach ($res as $row) {
                $dbCurrencies[$row['name']] = true;
            }
            $json = file_get_contents("http://api.fixer.io/$today?base=RUB");
            $obj = json_decode($json, true);
            self::$dateOfLastUpdate = $obj['date'];//date('Y-m-d',);
            $reverse = function ($val) { return number_format(1.0/$val, 2, ".", ""); };
            $tr = array_intersect_key($obj["rates"], $dbCurrencies);
            self::$rates = array_map($reverse, $tr);
        }
        return self::$rates;
    }
}