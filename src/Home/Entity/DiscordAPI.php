<?php

namespace Home\Entity;

use Befew\Request;
use Exception;

class DiscordAPI {
    private static ?DiscordAPI $instance = null;

    private const BASE_API_URL = 'https://discord.com/api';
    private const API_ENDPOINTS = [
        'me' => '/users/@me',
        'authorize' => '/oauth2/authorize',
        'token' => '/oauth2/token',
        'revoke' => '/oauth2/token/revoke',
    ];

    public static function getInstance(): DiscordAPI {
        if (self::$instance === null) {
            self::$instance = new DiscordAPI();
        }

        return self::$instance;
    }

    public function getAuthorizeURL(): string {
        $params = [
            'client_id' => OAUTH2_CLIENT_ID,
            'redirect_uri' => Request::getInstance()->getBaseURL(),
            'response_type' => 'code',
            'scope' => 'identify',
            'state' => '15773059ghq9183habn',
            'prompt' => 'consent'
        ];

        return self::BASE_API_URL . self::API_ENDPOINTS['authorize'] . '?' . http_build_query($params);
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getToken(): string {
        $code = Request::getInstance()->getGet('code');
        $postData = [
            'grant_type' => 'authorization_code',
            'client_id' => OAUTH2_CLIENT_ID,
            'client_secret' => OAUTH2_CLIENT_SECRET,
            'redirect_uri' => Request::getInstance()->getBaseURL(),
            'code' => $code
        ];

        $data = $this->apiRequest(self::BASE_API_URL . self::API_ENDPOINTS['token'], $postData);

        if (isset($data->error) && $data->error !== null) {
            throw new Exception($data->error_description);
        }

        return $data->access_token;
    }

    /**
     * @return Object
     * @throws Exception
     */
    public function getUserInfo(): object {
        $data = $this->apiRequest(self::BASE_API_URL . self::API_ENDPOINTS['me']);

        if (isset($data->code) && $data->code === 0) {
            throw new Exception($data->message);
        }

        return $data;
    }

    public function revokeToken(): void {
        $this->apiRequest(self::BASE_API_URL . self::API_ENDPOINTS['revoke'], [
            'token' => $_SESSION['access_token'],
            'token_type_hint' => 'access_token',
            'client_id' => OAUTH2_CLIENT_ID,
            'client_secret' => OAUTH2_CLIENT_SECRET,
        ]);
    }

    private function __construct() {}

    private function apiRequest($url, ?array $post = null, array $headers = []) {
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        if ($post !== null) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
        }

        $headers[] = 'Accept: application/json';

        if (array_key_exists('access_token', $_SESSION)) {
            $headers[] = 'Authorization: Bearer ' . $_SESSION['access_token'];
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        $response = curl_exec($ch);

        return json_decode($response);
    }
}