<?php

namespace french\avent\model;

use french\avent\model\RenderView;

class Frontend
{
    private $user;
    //private $baseUrl = 'avent.frenchdiscord.com';
    private $baseUrl = 'localhost/avent/index.php';

    public function home(string $apiURLBase)
    {
        $this->user = $this->apiRequest($apiURLBase);
        RenderView::render('template.php', 'home.php', ['user' => $this->user]);
    }

    public function login()
    {
        $param = [
            'client_id' => OAUTH2_CLIENT_ID,
            'redirect_uri' => REDIRECT_URI,
            'response_type' => 'code',
            'scope' => 'identify',
            'state' => '15773059ghq9183habn',
            'prompt' => 'consent'
        ];

        header('Location: https://discord.com/api/oauth2/authorize?' . http_build_query($param));
    }

    public function logout(string $revokeUrl, array $data = [])
    {
        $ch = curl_init($revokeUrl);

        curl_setopt_array($ch, array(
            CURLOPT_POST => TRUE,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
            CURLOPT_HTTPHEADER => array('Content-Type: application/x-www-form-urlencoded'),
            CURLOPT_POSTFIELDS => http_build_query($data),
        ));

        curl_exec($ch);
        unset($_SESSION['access_token']);
        header('Location: ' . $_SERVER['PHP_SELF']);
    }

    public function getToken(string $tokenURL, string $code)
    {
        $token = $this->apiRequest($tokenURL, array(
            "grant_type" => "authorization_code",
            'client_id' => OAUTH2_CLIENT_ID,
            'client_secret' => OAUTH2_CLIENT_SECRET,
            'redirect_uri' => REDIRECT_URI,
            'code' => $code
        ));

        $_SESSION['access_token'] = $token->access_token;
    
        header('Location: ' . $_SERVER['PHP_SELF']);
    }

    //-----------------------------//
    //           AJAX              //
    //-----------------------------//

    public function getWindowState()
    {
        if ($this->user) {
            $AdventManager = new AdventManager();
            $windowState = $AdventManager->getWindowState($this->user['id']);
            echo json_encode($windowState);
        } else {
            echo 'false';
        }
    }

    public function getReward()
    {
        if ($this->user) {
            $AdventManager = new AdventManager();
            //user is in guild or not
            //user is patreon or not
            //pick normal or special reward
            //get available reward or get amount won token
            //give reward token or ping lily/moi et user dans bot fun for special reward ()
            //return label reward to display
            //echo json_encode($windowState);
        } else {
            echo 'false';
        }
    }

    //-----------------------------//
    //          PRIVATE            //
    //-----------------------------//

    private function apiRequest($url, $post = null, $headers = []) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
      
        if($post) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
        }
      
        $headers[] = 'Accept: application/json';
      
        if($_SESSION['access_token']){
            $headers[] = 'Authorization: Bearer ' . $_SESSION['access_token'];
        }
      
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
       
        $response = curl_exec($ch);
        return json_decode($response);
    }
}