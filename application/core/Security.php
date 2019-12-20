<?php

namespace application\core;
use application\lib\Cache;

class Security {

  private const API_REQUESTS_LIMIT = 10;
  private const API_TIME_LIMIT = 10;
  private const API_BAN_TIME = 30;

  public static function checkForApiLimit() {
    $key = 'ip_info'.$_SERVER['REMOTE_ADDR'];

    $ipInfo = Cache::getValue($key);

    if($ipInfo !== false) {
      if($ipInfo < self::API_REQUESTS_LIMIT) {
        return Cache::incrementValue($key);
      }
      Cache::setValue($key, 10, self::API_BAN_TIME);
      return false;
    } else {
      return Cache::setValue($key, 0, self::API_TIME_LIMIT);
    }
  }

}
