<?php

namespace Home\Entity;

use Befew\Request;
use Befew\Response;
use Exception;

class DiscordAPI {
    private static ?DiscordAPI $instance = null;

    private const BASE_API_URL = 'https://discord.com/api';
    private const API_ENDPOINTS = [
        'me' => '/users/@me',
        'authorize' => '/oauth2/authorize',
        'token' => '/oauth2/token',
        'revoke' => '/oauth2/token/revoke',
        'guild' => '/guilds/' . GUILD_ID . '/members/',
        'event_message' => '/channels/' . EVENT_CHANNEL_SNOWFLAKE . '/messages',
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
            'scope' => 'guilds.members.read identify guilds',
            'state' => '15773059ghq9183habn',
            'prompt' => 'consent'
        ];

        return self::BASE_API_URL . self::API_ENDPOINTS['authorize'] . '?' . http_build_query($params);
    }

    /**
     * @return object
     * @throws Exception
     */
    public function getTokenData(): object {
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

        return $data;
    }

    /**
     * @return Object
     * @throws Exception
     */
    public function getUserInfo(): object {
        $data = $this->apiRequest(self::BASE_API_URL . self::API_ENDPOINTS['me']);

        if (isset($data->code) && $data->code === 0) {
            Request::getInstance()->destroySession();
            Response::redirect(Request::getInstance()->getBaseURL());
        }

        return $data;
    }

    /**
     * @param $user
     *
     * @return Object
     * @throws Exception
     */
    public function getGuildUserInfo($user): object {
        $data = $this->apiRequest(self::BASE_API_URL . self::API_ENDPOINTS['guild'] . $user->id, null, [], true);

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

    public function postEventMessage(string $content): void {
        $this->apiRequest(self::BASE_API_URL . self::API_ENDPOINTS['event_message'], [
            'token' => $_SESSION['access_token'],
            'token_type_hint' => 'access_token',
            'client_id' => OAUTH2_CLIENT_ID,
            'client_secret' => OAUTH2_CLIENT_SECRET,
            'content' => $content,
        ], [], true);
    }

    private function __construct() {}

    private function apiRequest($url, ?array $post = null, array $headers = [], bool $isBotToken = false) {
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        if ($post !== null) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
        }

        $headers[] = 'Accept: application/json';

        if (!$isBotToken && array_key_exists('access_token', $_SESSION)) {
            $headers[] = 'Authorization: Bearer ' . $_SESSION['access_token'];
        } else if ($isBotToken) {
            $headers[] = 'Authorization: Bot ' . BOT_TOKEN;
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        $response = curl_exec($ch);

        return json_decode($response);
    }
}