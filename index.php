<?php
session_start();

use french\avent\model\Frontend;

spl_autoload_register(
    function ($class) {
        $controllers = ['Frontend'];
        $array = explode('\\', $class);
        $nameClass = $array[count($array) - 1];

        if (in_array($nameClass, $controllers)) {
            require 'controller/' . $nameClass . '.php';
        } else {
            require 'model/' . $nameClass . '.php';
        }
    }
);
require_once('env.php');
define('OAUTH2_CLIENT_ID', $OAUTH2_CLIENT_ID);
define('OAUTH2_CLIENT_SECRET', $OAUTH2_CLIENT_SECRET);
define('REDIRECT_URI', 'http://localhost/avent/index.php');

$Frontend = new Frontend();
$authorizeURL = 'https://discord.com/api/oauth2/authorize';
$tokenURL = 'https://discord.com/api/oauth2/token';
$apiURLBase = 'https://discord.com/api/users/@me';
$revokeURL = 'https://discord.com/api/oauth2/token/revoke';

try {
    if ($_SESSION && $_SESSION['access_token']) {
        if ($_GET && $_GET['action']) {
            switch ($_GET['action']) {
                case 'logout':
                    $Frontend->logout($revokeURL, [
                        'token' => $_SESSION['access_token'],
                        'token_type_hint' => 'access_token',
                        'client_id' => OAUTH2_CLIENT_ID,
                        'client_secret' => OAUTH2_CLIENT_SECRET,
                    ]);
                    break;
                case 'getWindowState': 
                    $Frontend->getWindowState();
                    break;
                case 'getReward': 
                    $Frontend->getReward();
                    break;
            }
        } else {
            $Frontend->home($apiURLBase);
        }
    } else {
        if (!$_GET['code']) {
            $Frontend->login();
        } else {
            $Frontend->getToken($tokenURL, $_GET['code']);
        }
    }
} catch (Exception $error) {
    echo $error->getMessage();
}
