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
}