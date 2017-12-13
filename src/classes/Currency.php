<?php
class Currency
{
    public static function getCurrencies() {
        require_once __DIR__.'/../DBconfig.php';
        $db = DB::instance();
        $stmt = $db->prepare("
            SELECT * 
            FROM Currency");
        $stmt->execute();
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $res;
    }
}