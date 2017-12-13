<?php
/**
 * Created by PhpStorm.
 * User: linarkou
 * Date: 11/12/2017
 * Time: 00:06
 */

class User
{
    public static function verify($username, $password) {
        require_once __DIR__ . "/DB.php";
        $db = DB::instance();
        $queryFindUser = 'select * from Users 
                  where username like :usr';
        $stmt = $db->prepare($queryFindUser);
        $res = $stmt->execute(['usr'=>$username]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $correctpw = $row['password'];
        if ($res && password_verify($password, $correctpw))
            return $row['id'];
        else
            return false;
    }

    public static function create($username, $password)
    {
        require_once __DIR__ . "/DB.php";
        $db = DB::instance();
        $db->beginTransaction();
        $queryAddUser = 'insert into Users(username,password) 
                 values (:username,:password)';
        $stmt = $db->prepare($queryAddUser);
        try {
            $stmt->execute(['username' => $username, 'password' => $password]);
            $user_id = $db->lastInsertId();
            $db->commit();
            return $user_id;
        } catch (PDOException $ex) {
            $db->rollback();
            return false;
        }
    }

    public static function getAccountIDs($user_id) {
        require_once __DIR__.'/../DBconfig.php';
        $db = DB::instance();
        $stmt = $db->prepare("
            SELECT acc_id
            FROM User_Account 
            WHERE user_id = :user_id");
        $stmt->execute(['user_id'=>$user_id]);

        $res = array_map(
            function($row) {
                return $row['acc_id'];
            },
            $stmt->fetchAll(PDO::FETCH_ASSOC));
        return $res;
    }
}