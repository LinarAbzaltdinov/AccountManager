<?php
class Transaction
{
    /**
     * @param $trType array of transaction types: outcome | income | transafer
     */
    public static function getTransactions($trType, $acc_ids) {
        require_once __DIR__.'/../DBconfig.php';
        $db = DB::instance();
        $outcomeQuery = "SELECT T.*, 
                          (SELECT name FROM Accounts WHERE id = AC.acc_id) as acc_name,
                          (SELECT name FROM Currency WHERE id = AC.curr_id) as curr_name
                        FROM Transactions T LEFT JOIN Account_Currency AC ON T.acc_curr_id_from = AC.id
                        WHERE acc_curr_id_from IN (:acc_ids) 
                        AND acc_curr_id_to IS NULL";
        $incomeQuery = "SELECT T.*, 
                          (SELECT name FROM Accounts WHERE id = AC.acc_id) as acc_name,
                          (SELECT name FROM Currency WHERE id = AC.curr_id) as curr_name
                        FROM Transactions T LEFT JOIN Account_Currency AC ON T.acc_curr_id_to = AC.id
                        WHERE acc_curr_id_to IN (:acc_ids) 
                        AND acc_curr_id_from IS NULL";
        $transferQuery = "SELECT T.*, 
                          (SELECT name FROM Accounts WHERE id = AC.acc_id) as acc_name,
                          (SELECT name FROM Currency WHERE id = AC.curr_id) as curr_name
                        FROM Transactions T LEFT JOIN Account_Currency AC ON T.acc_curr_id_from = AC.id OR T.acc_curr_id_to = AC.id
                        WHERE acc_curr_id_to IN (:acc_ids) 
                        AND acc_curr_id_from IN (:acc_ids)";
        $queries = array();
        if (isset($trType['outcome']))
            array_push($queries, $outcomeQuery);
        if (isset($trType['income']))
            array_push($queries, $incomeQuery);
        if (isset($trType['transfer']))
            array_push($queries, $transferQuery);
        $resultedQuery = join(" UNION ", $queries) . " ORDER BY tr_date";
        $acc_curr_ids = array_map(function ($row) {return $row['id'];}, self::getAccCurrIdsFromAccId($acc_ids));
        $db = DB::instance();
        $stmt = $db->prepare($resultedQuery);
        $stmt->execute(['acc_ids'=>join(", ",$acc_curr_ids)]);
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $res;
    }

    private static function getAccCurrIdsFromAccId($acc_ids) {
        require_once __DIR__.'/../DBconfig.php';
        $db = DB::instance();
        $stmt = $db->prepare("SELECT id FROM Account_Currency WHERE acc_id IN (:acc_ids)");
        $stmt->execute(['acc_ids'=>join(", ", $acc_ids)]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function AddTransaction($acc_curr_id_from, $acc_curr_id_to, $cat_id, $value, $exchange_rate) {
        require_once __DIR__.'/../DBconfig.php';
        $db = DB::instance();
        if ($acc_curr_id_from == null)
            $acc_curr_id_from = "NULL";
        if ($acc_curr_id_to == null)
            $acc_curr_id_to = "NULL";
        if ($cat_id == null)
            $cat_id = "NULL";
        if ($exchange_rate == null)
            $exchange_rate = "NULL";
        $insertQuery = "INSERT INTO Transactions(acc_curr_id_from, acc_curr_id_to, cat_id, value, exchange_rate, tr_date)
                        VALUES ($acc_curr_id_from, $acc_curr_id_to, $cat_id, $value, $exchange_rate, NOW())";
        $db->query($insertQuery);

    }
}