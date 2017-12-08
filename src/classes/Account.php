<?php

class Account
{

    public static function createAcc($user_id, $acc_name, $currencies) {
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
        $res = createAccCurrencies($acc_id, $currencies, $db);
        if ($res)
            $db->commit();
        else
            $db->rollBack();
        return $res;
    }

    private static function createAccCurrencies($acc_id, $currencies, $db) {
        $insertQuery = "INSERT INTO Account_Currency (acc_id, curr_id, init_value) 
                        VALUES ($acc_id, :curr_id, :init_value)";
        return execQueryForArray($insertQuery, $currencies, $db);
    }

    private static function updateAccCurrencies($acc_id, $currencies, $db) {
        $updateQuery = "UPDATE Account_Currency 
                        SET init_value = :init_value
                        WHERE curr_id = :curr_id AND acc_id = $acc_id";
        return execQueryForArray($updateQuery, $currencies, $db);
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

    public static function updateAcc($acc_id, $new_acc_name, $currencies) {
        require_once __DIR__.'/../DBconfig.php';
        $db = DB::instance();
        $db->beginTransaction();
        $stmt = $db->prepare("UPDATE Accounts 
                                        SET name = :acc_name
                                        WHERE id =  $acc_id");
        $stmt->execute(['acc_name'=>$new_acc_name]);
        $exist_curr = $db->query("SELECT curr_id FROM Account_Currency WHERE acc_id = $acc_id")->fetchAll(PDO::FETCH_ASSOC);
        $updateCurrencies = array();
        $insertCurrencies = array();
        foreach ($currencies as $row) {
            foreach ($exist_curr as $exist) {
                if ($exist['curr_id'] == $row['curr_id']) {
                    array_unshift($insertCurrencies, $row);
                } else {
                    array_unshift($updateCurrencies, $row);
                }
            }
        }
        $res_ins = createAccCurrencies($acc_id, $insertCurrencies, $db);
        $res_upd = updateAccCurrencies($acc_id, $updateCurrencies, $db);
        if ($res_ins && $res_upd)
            $db->commit();
        else
            $db->rollBack();
        return $res_ins && $res_upd;
    }

    public static function closeAcc($acc_id) {
        require_once __DIR__.'/../DBconfig.php';
        $db = DB::instance();
        $res = $db->query("UPDATE Accounts SET closed = NOW() 
                                     WHERE id = $acc_id");
        return $res != false;
    }

    public static function getBalance($acc_id) {
        require_once __DIR__.'/DB.php';
        require_once __DIR__.'/../DBconfig.php';
        $db = DB::instance();
        $queryForInitValue = "SELECT ac_cur.id as acc_curr_id, ac_cur.init_value as value
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

        $db->beginTransaction();

        $stmt = $db->prepare($queryForInitValue);
        $stmt->execute(['acc_id'=>$acc_id]);
        $initValues = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt = $db->prepare($queryForTransactions);
        $stmt->execute(['acc_id'=>$acc_id]);
        $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $db->commit();

        $acc_values = array(); //acc_curr_id => value
        foreach ($initValues as $row) {
            $acc_values[$row['acc_curr_id']] = $row['value'];
        }
        foreach ($transactions as $row) {
            if ($row['acc_curr_id_from'] != null && isset($acc_values[$row['acc_curr_id_from']])) {
                $acc_values[$row['acc_curr_id_from']] -= $row['value'];
            }
            if ($row['acc_curr_id_to'] != null && isset($acc_values[$row['acc_curr_id_to']])) {
                $v = $row['value'];
                if ($row['exchange_rate'] != null) {
                    global $v;
                    $v *= $row['exchange_rate'];
                }
                $acc_values[$row['acc_curr_id_to']] += $v;
            }
        }
        //выбирает ненужные счета!
        return $acc_values;
    }
}