<?php

namespace application\models\Users;
use application\core\Model;
use application\lib\Db;
use application\core\ErrorCodes;

class Storage extends Model {

    public static function createUser($firstname, $lastname, $email, $password) {
        if(self::emailExists($email)['response'] == true) {
            return [
                'error_code' => ErrorCodes::EMAIL_ALREADY_EXISTS,
                'details' => $email,
                'http' => 400,
            ];
        }

        $sql = 'INSERT INTO users (status, firstname, lastname, email, password) VALUES (:status, :firstname, :lastname, :email, :pass)';
        $replacement = [':status' => 0, ':firstname' => $firstname, ':lastname' => $lastname, ':email' => $email, ':pass' => password_hash($password, PASSWORD_BCRYPT)];

        $result = Db::queryExecuteResult($sql, $replacement);

        if($result) {
            return [
                'response' => true,
                'http' => 201,
            ];
        }

        return [
            'error_code' => ErrorCodes::UNKNOWN_INTERNAL_ERROR,
            'details' => false,
            'http' => 500,
        ];
    }

    public static function emailExists($email) {
        $sql = 'SELECT id FROM users WHERE email = :email';
        $replacement = [':email' => $email];

        $column = Db::queryColumn($sql, $replacement);

        if($column) {
            return [
                'response' => true,
                'http' => 200,
            ];
        }

        return [
            'response' => false,
            'http' => 200,
        ];
    }

    public static function idExists($id) {
        $sql = 'SELECT id FROM users WHERE id = :id';
        $replacement = [':id' => $id];

        $column = Db::queryColumn($sql, $replacement);

        if($column) {
            return [
                'response' => true,
                'http' => 200,
            ];
        }

        return [
            'response' => false,
            'http' => 200,
        ];
    }

    private static function refreshTokenExists($refreshToken) {
        $sql = 'SELECT id FROM users WHERE refresh_token = :refresh';
        $replacement = [':refresh' => $refreshToken];

        $column = Db::queryColumn($sql, $replacement);

        if($column) {
            return [
                'response' => true,
                'http' => 200,
            ];
        }

        return [
            'response' => false,
            'http' => 200,
        ];
    }

    public static function getUserInformationByEmail($email) {
        if(self::emailExists($email)['response'] == true) {
            $sql = 'SELECT * FROM users WHERE email = :email';
            $replacement = [':email' => $email];
    
            $result = Db::querySingleAssoc($sql, $replacement);

            return [
                'response' => $result,
                'http' => 200,
            ];
        }
        
        return [
            'error_code' => ErrorCodes::EMAIL_IS_NOT_REGISTERED,
            'details' => $email,
            'http' => 400,
        ];
    }

    public static function getUserInformationById($id) {
        if(self::idExists($id)['response'] == true) {
            $sql = 'SELECT * FROM users WHERE id = :id';
            $replacement = [':id' => $id];
    
            $result = Db::querySingleAssoc($sql, $replacement);

            return [
                'response' => $result,
                'http' => 200,
            ];
        }
        
        return [
            'error_code' => ErrorCodes::ID_IS_NOT_REGISTERED,
            'details' => $id,
            'http' => 400,
        ];
    }

    public static function addRefreshTokenToId($refreshToken, $id) {
        $sql = 'UPDATE users SET refresh_token = :token WHERE id = :id';
        $replacement = [':token' => $refreshToken, ':id' => $id];

        $result = Db::queryExecuteResult($sql, $replacement);

        if($result) {
            return [
                'response' => true,
                'http' => 200,
            ];
        }

        return [
            'error_code' => ErrorCodes::UNKNOWN_INTERNAL_ERROR,
            'details' => false,
            'http' => 500,
        ];
    }

    public static function setAvatar($id, $avatar) {
        $sql = 'UPDATE users SET avatar = :avatar WHERE id = :id';
        $replacement = [':avatar' => $avatar, ':id' => $id];

        $result = Db::queryExecuteResult($sql, $replacement);

        if($result) {
            return [
                'response' => true,
                'http' => 200,
            ];
        }

        return [
            'error_code' => ErrorCodes::UNKNOWN_INTERNAL_ERROR,
            'details' => false,
            'http' => 500,
        ];
    }

}