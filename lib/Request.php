<?php

namespace Befew;

class Request extends Utils {
    private static ?Request $instance = null;

    private Path $url;

    public static function getInstance(): Request {
        if (self::$instance === null) {
            self::$instance = new Request();
        }

        return self::$instance;
    }

    /**
     * @see https://stackoverflow.com/a/8891890
     *
     * @return string
     */
    public function getURL(): string {
        return $this->url->getPath();
    }

    public function getBaseURL(): string {
        return $this->getProtocol() . '://' . $this->getHost();
    }

    public function isSSL(): bool {
        return !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
    }

    public function getProtocol(): string {
        $protocol = strtolower($_SERVER['SERVER_PROTOCOL']);

        return substr($protocol, 0, strpos($protocol, '/')) . ($this->isSSL() ? 's' : '');
    }

    public function getHost(): string {
        $port = $_SERVER['SERVER_PORT'];
        $port = (!$this->isSSL() && $port === '80') || ($this->isSSL() && $port === '443') ? '' : ':' . $port;
        $host = $_SERVER['HTTP_HOST'] ?? null;

        return $host ?? $_SERVER['SERVER_NAME'] . $port;
    }

    public function __toString(): string {
        return $this->getURL();
    }

    /**
     * @param string|null $key
     * @param null $default
     * @param bool $secure
     *
     * @return string|int|array|null
     */
    public function getPost(?string $key = null, $default = null, bool $secure = false) {
        return ($key === null) ? $this->getVar($_POST, $default, $secure) : $this->getVar($_POST[$key], $default, $secure);
    }

    /**
     * @param string|null $key
     * @param null $default
     * @param bool $secure
     *
     * @return string|int|array|null
     */
    public function getGet(?string $key = null, $default = null, bool $secure = false) {
        return ($key === null) ? $this->getVar($_GET, $default, $secure) : $this->getVar($_GET[$key], $default, $secure);
    }

    public function hasGet(string $key): bool {
        return array_key_exists($key, $_GET);
    }

    public function hasPost(string $key): bool {
        return array_key_exists($key, $_POST);
    }

    public function has(string $key): bool {
        return $this->hasGet($key) || $this->hasPost($key);
    }

    /**
     * @param string $key
     * @param null $default
     * @param bool $secure
     * @param string $type
     *
     * @return string|int|array|null
     */
    public function get(string $key, $default = null, bool $secure = false, string $type = 'all') {
        switch(strtolower($type)) {
            case 'get':
                return $this->getGet($key, $default, $secure);
                break;

            case 'post':
                return $this->getPost($key, $default, $secure);
                break;

            default:
                return ($this->getGet($key) === null) ? $this->getPost($key, $default, $secure) : $this->getGet($key, $default, $secure);
                break;
        }
    }

    public function createSession(): void {
        $_SESSION['loggedIn'] = true;
    }

    public function destroySession(): void {
        unset($_SESSION['loggedIn']);
        session_destroy();
    }

    public function isPostData(): bool {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    public function isUserLoggedIn(): bool {
        return isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] === true;
    }

    private function __construct() {
        $this->url = new Path($this->getProtocol() . '://' . $this->getHost() . $_SERVER['REQUEST_URI']);
    }
}