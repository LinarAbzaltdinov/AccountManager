<?php

class Account
{

    public static function createAcc($user_id, $acc_name, $currencies_initValues) {
        require_once __DIR__.'/../DBconfig.php';
        $db = DB::instance();
        $db->beginTransaction();
        $stmt = $db->prepare("INSERT INTO Accounts (name, opened) 
                                        VALUES (:acc_name, NOW())");
        $stmt->execute(['acc_name'=>$acc_name]);
        $acc_id = $db->lastInsertId();
        $stmt = $db->prepare("INSERT INTO User_Account 
                                        VALUES (:user_id, :acc_id)");
        $stmt->execute(['user_id'=>$user_id, 'acc_id'=>$acc_id]);
        $res = self::createAccCurrencies($acc_id, $currencies_initValues, $db);
        if ($res)
            $db->commit();
        else
            $db->rollBack();
        return $res;
    }

    public static function updateAcc($acc_id, $new_acc_name, $currencies_initValues) {
        require_once __DIR__.'/../DBconfig.php';
        $db = DB::instance();
        $db->beginTransaction();
        $stmt = $db->prepare("UPDATE Accounts 
                                        SET name = :acc_name
                                        WHERE id =  $acc_id");
        $stmt->execute(['acc_name'=>$new_acc_name]);
        $exist_curr = $db->query("SELECT curr_id FROM Account_Currency WHERE acc_id = $acc_id")->fetchAll(PDO::FETCH_ASSOC);
        $updateCurrenciesAndValues = array();
        $insertCurrenciesAndValues = array();
        foreach ($currencies_initValues as $row) {
            $found = false;
            for ($i = 0; $i < count($exist_curr); ++$i) {
                if ($exist_curr[$i]['curr_id'] == $row['curr_id']) {
                    array_push($updateCurrenciesAndValues, $row);
                    array_splice($exist_curr, $i, 1);
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                array_push($insertCurrenciesAndValues, $row);
            }
        }
        $deleteCurrencies = $exist_curr;

        $res_ins = self::createAccCurrencies($acc_id, $insertCurrenciesAndValues, $db);
        $res_upd = self::updateAccCurrencies($acc_id, $updateCurrenciesAndValues, $db);
        $res_del = self::deleteAccCurrencies($acc_id, $deleteCurrencies, $db);
        if ($res_ins && $res_upd && $res_del)
            $db->commit();
        else
            $db->rollBack();
        return $res_ins && $res_upd && $res_del;
    }

    public static function closeAcc($acc_id) {
        require_once __DIR__.'/../DBconfig.php';
        $db = DB::instance();
        $res = $db->query("UPDATE Accounts SET closed = NOW() 
                                     WHERE id = $acc_id");
        return $res != false;
    }

    private static function createAccCurrencies($acc_id, $currencies_initValues, $db) {
        $insertQuery = "INSERT INTO Account_Currency (acc_id, curr_id, init_value) 
                        VALUES ($acc_id, :curr_id, :init_value)";
        return self::execQueryForArray($insertQuery, $currencies_initValues, $db);
    }

    private static function deleteAccCurrencies($acc_id, $currencies, $db) {
        $deleteQuery = "DELETE FROM Account_Currency                         
                        WHERE curr_id = :curr_id";
        return self::execQueryForArray($deleteQuery, $currencies, $db);
    }

    private static function updateAccCurrencies($acc_id, $currencies_initValues, $db) {
        $updateQuery = "UPDATE Account_Currency 
                        SET init_value = :init_value
                        WHERE curr_id = :curr_id AND acc_id = $acc_id";
        return self::execQueryForArray($updateQuery, $currencies_initValues, $db);
    }

    private static function execQueryForArray($query, $array, $db) {
        $res = true;
        foreach ($array as $row) {
            $stmt = $db->prepare($query);
            foreach ($row as $column => $value) {
                $stmt->bindValue(":{$column}", $value);
            }
            $res = $stmt->execute();
            if ($res == false)
                break;
            $stmt = null;
        }
        return $res;
    }

    private static function getBalance($acc_id) {
        require_once __DIR__.'/DB.php';
        require_once __DIR__.'/../DBconfig.php';
        $db = DB::instance();
        $queryForInitValue = "SELECT ac_cur.id as acc_curr_id, ac_cur.init_value as value, ac.name as name
                              FROM Accounts ac
                              INNER JOIN Account_Currency ac_cur ON ac.id=ac_cur.acc_id
                              WHERE ac.id = :acc_id AND ac.closed IS NULL";
        $queryForTransactions =
           "SELECT tr.*
            FROM 
                Account_Currency ac_cur 
            LEFT JOIN 
                Transactions tr 
            ON 
                (ac_cur.id = tr.acc_curr_id_from OR ac_cur.id = tr.acc_curr_id_to)
            WHERE 
                ac_cur.id IN 
                    (SELECT acr.id
                    FROM Accounts ac
                    INNER JOIN Account_Currency acr ON ac.id=acr.acc_id
                    WHERE ac.closed IS NULL
                    AND ac.id = :acc_id)";
        $stmt = $db->prepare($queryForInitValue);
        $stmt->execute(['acc_id'=>$acc_id]);
        $initValues = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt = $db->prepare($queryForTransactions);
        $stmt->execute(['acc_id'=>$acc_id]);
        $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $acc_values = array();
        foreach ($initValues as $row) {
            $acc_values[$row['acc_curr_id']] = $row['value'];
        }
        foreach ($transactions as $row) {
            if (array_key_exists($row['acc_curr_id_from'], $acc_values) && isset($acc_values[$row['acc_curr_id_from']])) {
                $acc_values[$row['acc_curr_id_from']] -= $row['value'];
            }
            if (array_key_exists($row['acc_curr_id_to'], $acc_values) && isset($acc_values[$row['acc_curr_id_to']])) {
                $v = $row['value'];
                if ($row['exchange_rate'] != null)
                    $v *= $row['exchange_rate'];
                $acc_values[$row['acc_curr_id_to']] += $v;
            }
        }
        return $acc_values;
    }

    public static function getExtendedInfo($acc_id) {
        require_once __DIR__.'/../DBconfig.php';
        $db = DB::instance();
        $query="SELECT *
                FROM Accounts
                WHERE id = :acc_id";
        $stmt = $db->prepare($query);
        $stmt->execute(['acc_id'=>$acc_id]);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($res['closed'] != null)
            return $res;
        $query="SELECT curr_id, 
                  (SELECT name FROM Currency WHERE id=curr_id) as curr_name,
                  id,
                  init_value
                FROM Account_Currency
                WHERE acc_id = :acc_id";
        $stmt = $db->prepare($query);
        $stmt->execute(['acc_id'=>$acc_id]);
        $res['acc_curr'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $currentBalance = self::getBalance($acc_id);
        for ($i = 0; $i < count($res['acc_curr']); ++$i) {
            $res['acc_curr'][$i]['current_value'] = $currentBalance[$res['acc_curr'][$i]['id']];
        }
        return $res;
    }

    public static function getInfo($acc_id) {
        require_once __DIR__.'/../DBconfig.php';
        $db = DB::instance();
        $query="SELECT *
                FROM Accounts
                WHERE id = :acc_id";
        $stmt = $db->prepare($query);
        $stmt->execute(['acc_id'=>$acc_id]);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        return $res;
    }
}