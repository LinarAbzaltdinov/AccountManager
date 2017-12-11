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
        $today = date("Y-m-d");
        $lastUpdateWeekday = date('w', self::$dateOfLastUpdate);
        if (!isset(self::$dateOfLastUpdate) ||
            $today != self::$dateOfLastUpdate &&
            !($lastUpdateWeekday == 5 && date_diff(self::$dateOfLastUpdate, $today) <= 2)) // don't update at weekend
        {
            $json = file_get_contents("http://api.fixer.io/$today?base=RUB");
            $obj = json_decode($json, true);
            self::$dateOfLastUpdate = $obj['date'];//date('Y-m-d',);
            $func = function ($val) { return 1/$val; };
            self::$rates = array_map($func, obj['rates']);
        }
        return self::$rates;
    }
}