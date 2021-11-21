<?php

namespace Home;

use Befew\Controller;
use Befew\Request;
use Befew\Response;
use Exception;
use Home\Entity\CalendarOpenedWindow;
use Home\Entity\DiscordAPI;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class HomeController extends Controller {
    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function indexAction(): void {
        $this->template->addCSS('default.css');
        $this->template->addCSS('https://fonts.googleapis.com/css2?family=Roboto&display=swap');

        $this->template->addJS('ajax', false);
        $this->template->addJS('parts', false);
        $this->template->addJS('home', false);
        $this->template->addJS('openWindow', false);

        if (Request::getInstance()->isUserLoggedIn()) {
            $user = DiscordAPI::getInstance()->getUserInfo();
            $discordAvatarExtension = strpos($user->avatar, 'a_') === 0 ? '.gif' : '.png';
            $discordAvatarURL = 'https://cdn.discordapp.com/avatars/' . $user->id . '/' . $user->avatar . $discordAvatarExtension . '?size=24';

            $this->template->render('index.html.twig', [
                'user' => $user,
                'discordAvatarURL' => $discordAvatarURL,
            ]);
        } else {
            if (!Request::getInstance()->hasGet('code')) {
                Response::redirect(DiscordAPI::getInstance()->getAuthorizeURL());
            } else {
                DiscordAPI::getInstance()->getToken();
                Request::getInstance()->createSession();
                Response::redirect(Request::getInstance()->getBaseURL());
            }
        }
    }

    public function logoutAction(): void {
        DiscordAPI::getInstance()->revokeToken();
        Request::getInstance()->destroySession();
        Response::redirect(Request::getInstance()->getBaseURL());
    }

    /**
     * @throws Exception
     */
    public function getOpenedWindowsAction(): void {
        if (Request::getInstance()->isUserLoggedIn()) {
            $user = DiscordAPI::getInstance()->getUserInfo();
            $openedWindows = CalendarOpenedWindow::getAllForUser($user->id);

            echo json_encode($openedWindows);
        } else {
            echo json_encode(false);
        }
    }

    /**
     * @throws Exception
     */
    public function getRewardAction(): void {
        if (Request::getInstance()->isUserLoggedIn()) {
            $user = DiscordAPI::getInstance()->getUserInfo();
            // user is in guild or not
            // user is patreon or not
            // pick normal or special reward
            // get available reward or get amount won token
            // give reward token or ping lily/moi et user dans bot fun for special reward ()
            // return label reward to display
        } else {
            echo json_encode(false);
        }
    }
}