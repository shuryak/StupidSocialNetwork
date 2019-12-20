<?php

namespace application\core;
use application\core\Security;

class Router {

    protected static $routes = [];
    protected static $routesArray = [];
    protected static $params = [];

    public static function addRoutes() {
        $allowedRoutes = require 'application/config/routes.php';
        foreach($allowedRoutes as $route => $routeArray) {
            self::add($route, $routeArray);
        }
    }

    public static function addApi() {
        $allowedRoutes = require 'application/config/api.php';
        foreach($allowedRoutes as $route => $routeArray) {
            self::add($route, $routeArray);
        }
    }

    public static function isApi($route) {
        return preg_match('/api\/(.*)\.(.*)/', $route); // Incomplete regex
    }

    public static function add($route, $routeArray) {
        $route = '#^'.$route.'$#';
        self::$routes[$route] = $routeArray;
    }

    public static function matchRoute() {
        $url = trim($_SERVER['REQUEST_URI'], '/');
        foreach(self::$routes as $route => $routeArray) {
            if(preg_match($route, parse_url($url, PHP_URL_PATH))) {
                self::$routesArray = $routeArray;
                self::$params = parse_url($url, PHP_URL_QUERY) != NULL ? parse_url($url, PHP_URL_QUERY) : '';
                return true;
            }
        }
        return false;
    }

    public static function matchApi() {
        // $url = trim($_SERVER['REQUEST_URI'], '/api');
        $url = str_replace('/api/', '', $_SERVER['REQUEST_URI']);
        // var_dump($url);
        list($controller, $method) = explode(".", $url);
        foreach(self::$routes as $route => $routeArray) {
            if(preg_match($route, parse_url($url, PHP_URL_PATH))) {
                self::$routesArray = $routeArray;
                self::$params = parse_url($url, PHP_URL_QUERY) != NULL ? $_GET : '';
                return true;
            }
        }
        return false;
    }

    public static function run() {
        if(self::isApi(trim($_SERVER['REQUEST_URI'], '/'))) {
            if(!Security::checkForApiLimit()) {
                exit(View::errorCode(429));
            }
            self::addApi();
            if(self::matchApi()) {
                $path = 'application\controllers\\'.ucfirst(self::$routesArray['controller']).'Controller';
                if(class_exists($path)) {
                    $method = self::$routesArray['method'].'Api';
                    if(method_exists($path, $method)) {
                        $path::load(self::$routesArray);
                        $path::$method();
                    } else {
                        View::errorCode(404);
                    }
                }
                else {
                    View::errorCode(404);
                }
            } else {
                View::errorCode(404);
            }
        } else {
            self::addRoutes();
            if(self::matchRoute()) {
                $path = 'application\controllers\\'.ucfirst(self::$routesArray['controller']).'Controller';
                if(class_exists($path)) {
                    $action = self::$routesArray['action'].'Action';
                    if(method_exists($path, $action)) {
                        $path::load(self::$routesArray);
                        $path::$action();
                    } else {
                        View::errorCode(404);
                    }
                } else {
                    View::errorCode(404);
                }
            } else {
                View::errorCode(404);
            }
        }
    }
}
