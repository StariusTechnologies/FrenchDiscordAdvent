<?php

namespace Befew;

class Router {
    public static function dispatch(): void {
        $routeFound = false;
        $url = Request::getInstance()->getGet('page', 'index', true);
        $routes = yaml_parse_file(BEFEW_BASE_URL . 'app/routes.yml');

        foreach ($routes as $key => $path) {
            if (strpos($url, $key) === 0) {
                $class = str_replace('/', '\\', substr($path['file'], 0, strpos($path['file'], '.')));
                new $class($path['action']);
                $routeFound = true;

                break;
            }
        }

        if (!$routeFound) {
            Response::throwStatus(404);
        }
    }
}
