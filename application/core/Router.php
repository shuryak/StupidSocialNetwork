<?php

namespace application\core;
use application\core\Security;

class Router {
    private static $routes = []; // Allowed Routes

    // This method gets allowed routes from config files
    private static function getAllowedRoutes() {
        // Allowed routes are routes from the config files
        $allowedRoutes = array_merge(require 'application/config/routes.php', require 'application/config/api.php');

        $regexAllowedRoutes = [];

        foreach($allowedRoutes as $route => $routeArray) {
            $regexAllowedRoutes['#^'.$route.'$#'] = $routeArray; // Preparing for regex
        }

        return $regexAllowedRoutes;
    }

    // This method checks route string for api route
    private static function isApi($route) {
        return preg_match('/api\/(.*)\.(.*)/', $route); // Incomplete regex (!)
    }

    // This method finds matches request url with allowed routes
    private static function matchRoute($isApi = false) {
        if($isApi) $url = str_replace('/api/', '', $_SERVER['REQUEST_URI']);
        else $url = trim($_SERVER['REQUEST_URI'], '/');

        foreach(self::$routes as $route => $routeArray) { // Enumeration of allowed routes
            if(preg_match($route, parse_url($url, PHP_URL_PATH))) { // Match check
                return [
                    'routes_array' => $routeArray,
                    'params' => parse_url($url, PHP_URL_QUERY) != NULL ? $_GET : ''
                ];
            }
        }

        return false;
    }

    public static function run() {
        self::$routes = self::getAllowedRoutes();

        /*
        Controller actions have the following format: <Controller_Name>::<Action_Name>Action;
        Controller api methods have the following format: <Controller_Name>::<Method_Name>Api.
        */
        if(self::isApi(trim($_SERVER['REQUEST_URI'], '/'))) {
            if(!Security::checkForApiLimit()) exit(View::errorCode(429)); // If the request limit is exceeded, throw an http error 429

            $matchResult = self::matchRoute(true);
            $method = $matchResult['routes_array']['method'].'Api';
        } else {
            $matchResult = self::matchRoute();
            $method = $matchResult['routes_array']['action'].'Action';
        }

        $path = 'application\controllers\\'.ucfirst($matchResult['routes_array']['controller']).'Controller'; // Path of controller

        if($matchResult && class_exists($path) && method_exists($path, $method)) {
            // All controllers extends core/Controller.php which contains method load()
            $path::load($matchResult['routes_array']);
            $path::$method();
        } else {
            View::errorCode(404);
        }
    }
}
