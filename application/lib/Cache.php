<?php

namespace application\lib;
use Exception;
use Memcached;

class Cache {

  protected static $cacheServer;

  public static function init() {
    try {
      self::$cacheServer = new Memcached();
      self::$cacheServer->addServer('127.0.0.1', 11211);
    } catch(Exception $e) {
      exit('Something went wrong!');
    }
  }

  public static function getValue($key) {
    self::init();

    return self::$cacheServer->get($key);
  }

  public static function setValue($key, $value, $expiration = 20) {
    self::init();

    $result = self::$cacheServer->set($key, $value, time() + $expiration);

    if($result) {
      return true;
    }

    return false;
  }

  public static function incrementValue($key, $offset = 1) {
    self::init();

    $result = self::$cacheServer->increment($key, $offset);

    if($result) {
      return true;
    }

    return false;
  }

}
