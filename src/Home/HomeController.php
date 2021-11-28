<?php

namespace Home;

use Befew\Controller;
use Befew\Path;
use Befew\Request;
use Befew\Response;
use Exception;
use Home\Entity\CalendarOpenedWindow;
use Home\Entity\CalendarStory;
use Home\Entity\Reward;
use Home\Entity\MemberToken;
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

        $this->template->addJS('calendar.js');
        $this->template->addJS('ajax.js', false);
        $this->template->addJS('parts.js', false);
        $this->template->addJS('home.js', false);

        if (Request::getInstance()->isUserLoggedIn()) {
            $user = DiscordAPI::getInstance()->getUserInfo();
            $discordAvatarExtension = strpos($user->avatar, 'a_') === 0 ? '.gif' : '.png';
            $discordAvatarURL = 'https://cdn.discordapp.com/avatars/' . $user->id . '/' . $user->avatar . $discordAvatarExtension . '?size=24';

            $relativeCalendarImagesFolderPath = (clone $this->assetsPath)
                ->concat('images', 'calendar-windows')
                ->withTrailingSlash()
                ->withWebSeparators();

            $absoluteCalendarImagesPath = new Path(__DIR__, 'View', 'images', 'calendar-windows', '*');
            $calendarImages = array_map(function (string $path) use ($relativeCalendarImagesFolderPath): string {
                $filename = substr($path, strripos($path, DIRECTORY_SEPARATOR) + 1);

                return $relativeCalendarImagesFolderPath . $filename;
            }, glob($absoluteCalendarImagesPath));

            $this->template->render('index.html.twig', [
                'user' => $user,
                'discordAvatarURL' => $discordAvatarURL,
                'calendarImages' => $calendarImages,
            ]);
        } else {
            if (!Request::getInstance()->hasGet('code')) {
                Response::redirect(DiscordAPI::getInstance()->getAuthorizeURL());
            } else {
                $_SESSION['access_token'] = DiscordAPI::getInstance()->getToken();
                Request::getInstance()->createSession();
                Response::redirect(Request::getInstance()->getBaseURL());
            }
        }
    }

    public function logoutAction(): void {
        DiscordAPI::getInstance()->revokeToken();
        unset($_SESSION['access_token']);
        Request::getInstance()->destroySession();
        Response::redirect(Request::getInstance()->getBaseURL());
    }

    /**
     * @throws Exception
     */
    public function getRewardAction(): void {
        $user = DiscordAPI::getInstance()->getUserInfo();
        $windowNumber = Request::getInstance()->getGet('window');
        $dayNumber = date('d');
        $isTodayWindow = $windowNumber && $windowNumber == $dayNumber;
        $canOpenTodayWindow = CalendarOpenedWindow::canOpenTodayWindow($user, $dayNumber);

        if (Request::getInstance()->isUserLoggedIn() && $isTodayWindow && $canOpenTodayWindow) {
            $reward = Reward::getInstance()->pickReward($user);
            CalendarOpenedWindow::openWindow($user->id, $dayNumber, $reward['label'], $reward['label'] === Reward::REWARD_LABEL_NITRO);
            
            if ($reward['label'] === Reward::REWARD_LABEL_TOKEN) {
                MemberToken::getInstance()->giveTokens($user->id, $reward['amount']);
            } else {
                $displayedLabels = $reward['label'] === Reward::REWARD_LABEL_NITRO
                    ? [
                        'fr' => Reward::DISPLAYED_FR_LABEL_NITRO,
                        'en' => Reward::DISPLAYED_EN_LABEL_NITRO
                    ]
                    : [
                        'fr' => Reward::DISPLAYED_FR_LABEL_PATREON,
                        'en' => Reward::DISPLAYED_EN_LABEL_PATREON
                    ];

                Reward::getInstance()->pingForSpecialReward($user->id, $displayedLabels);
            }

            echo json_encode([
                'reward' => $reward,
                'story' => CalendarStory::getTodayStory()
            ]);
        } else {
            echo json_encode(false);
        }
    }
}