<?php

namespace Befew;

class Response {
    public static function throwStatus(int $code): void {
        http_response_code($code);

        switch ($code) {
            default:
                include(dirname(__FILE__, 2) . '/app/404.php');
                break;
        }
    }

    public static function redirect(string $url, int $status = 302): void {
        http_response_code($status);
        header('Location: ' . $url);
    }
}