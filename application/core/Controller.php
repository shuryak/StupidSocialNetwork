<?php

namespace application\core;

abstract class Controller {

    public static $route;
    public static $view;
    public static $model;

    public static function load($route) {
        self::$route = $route;
        if(isset($route['action'])) {
            self::$view = new View;
            self::$view::load($route);
        }
        self::$model = self::loadModel($route['controller']);
    }
    
    public static function loadModel($name) {
        $path = 'application\models\\'.ucfirst($name);
        if(class_exists($path)) {
            return new $path;
        }
    }
}