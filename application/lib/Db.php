<?php

namespace application\lib;
use PDO;
use Exception;

class Db {

    protected static $db;

    public static function init() {
        try {
            self::$db = new PDO('sqlite:projectDB.db');

            self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $tables = require 'application/config/db-tables.php';
            foreach($tables as $sql) {
                self::$db->exec($sql);
            }
        } catch(Exception $e) {
            exit($e->getMessage());
        }
        
    }

    public static function queryAssoc($sql, $replacement = []) {
        self::init();

        $stmt = self::$db->prepare($sql);
        $stmt->execute($replacement);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function querySingleAssoc($sql, $replacement = []) {
        self::init();

        $stmt = self::$db->prepare($sql);
        $stmt->execute($replacement);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function queryColumn($sql, $replacement = []) {
        self::init();

        $stmt = self::$db->prepare($sql);
        $stmt->execute($replacement);

        return $stmt->fetch(PDO::FETCH_COLUMN);
    }

    public static function queryExecuteResult($sql, $replacement = []) {
        self::init();

        $stmt = self::$db->prepare($sql);
        return $stmt->execute($replacement);
    }

}