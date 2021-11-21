<?php

namespace Befew;

class Utils {
    /**
     * @param $var
     * @param null $default
     * @param bool $secure
     *
     * @return string|int|array|null
     */
    public static function getVar(&$var, $default = null, bool $secure = false) {
        if (!isset($var)) {
            return $default;
        } else if (empty($var)) {
            return $default;
        } else {
            if ($secure) {
                return htmlspecialchars($var);
            } else {
                return $var;
            }
        }
    }

    public static function getQueryWithValues(string $query, array $values): string {
        return strtr($query, array_map(function($v) {return '`' . $v . '`';}, $values));
    }

    public static function delete(string $path): bool {
        if (is_dir($path)) {
            $files = array_diff(scandir($path), ['.', '..']);

            foreach ($files as $file) {
                self::delete(realpath($path) . '/' . $file);
            }

            return rmdir($path);
        } else if (is_file($path)) {
            return unlink($path);
        }

        return false;
    }

    public static function cryptPassword(string $password, string $salt): string {
        return sha1(sha1(BEFEW_SECRET_TOKEN) . sha1($password) . sha1($salt));
    }
}