<?php

namespace application\models;
use application\core\Model;
use application\models\Users\Storage;

class Users {

    public static function emailExists($email) {
        return Storage::emailExists($email);
    }

    public static function idExists($id) {
        return Storage::idExists($id);
    }

    public static function createUser($firstname, $lastname, $email, $password) {
        return Storage::createUser($firstname, $lastname, $email, $password);
    }

    public static function getUserInformationByEmail($email) {
        return Storage::getUserInformationByEmail($email);
    }

    public static function getUserInformationById($id) {
        return Storage::getUserInformationById($id);
    }

    public static function addRefreshTokenToId($refreshToken, $id) {
        return Storage::addRefreshTokenToId($refreshToken, $id);
    }

}