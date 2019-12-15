<?php

namespace application\lib;

class FileName {
  
  public static function random($id, $path, $extension)
  {
    $extension = $extension ? '.'.$extension : '';
    $path = $path ? $path.'/' : '';

    do {
      $name = md5(microtime().rand(0, 9999));
      $file = $path.$id.'_'.$name.$extension;
    } while (file_exists($file));

    return $id.'_'.$name;
  }

}