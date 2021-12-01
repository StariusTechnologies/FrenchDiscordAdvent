<?php

namespace Home;

use Befew\Controller;
use Befew\Path;
use Befew\Request;
use Befew\Response;
use DateTime;
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

        $this->template->addJS('modal.js');
        $this->template->addJS('calendar.js');
        $this->template->addJS('ajax.js', false);
        $this->template->addJS('parts.js', false);
        $this->template->addJS('home.js', false);

        if (Request::getInstance()->isUserLoggedIn()) {
            $user = DiscordAPI::getInstance()->getUserInfo();
            $discordAvatarExtension = strpos($user->avatar, 'a_') === 0 ? '.gif' : '.png';
            $discordAvatarURL = 'https://cdn.discordapp.com/avatars/' . $user->id . '/' . $user->avatar . $discordAvatarExtension . '?size=24';

            $goalDate = new DateTime('2022-01-01');
            $daysLeft = ceil(($goalDate->getTimestamp() - time()) / (24 * 60 * 60));

            $relativeCalendarActiveImagesFolderPath = (clone $this->assetsPath)
                ->concat('images', 'calendar-windows', 'active')
                ->withTrailingSlash()
                ->withWebSeparators();

            $relativeCalendarInactiveImagesFolderPath = (clone $this->assetsPath)
                ->concat('images', 'calendar-windows', 'inactive')
                ->withTrailingSlash()
                ->withWebSeparators();

            $absoluteCalendarActiveImagesPath = new Path(__DIR__, 'View', 'images', 'calendar-windows', 'active', '*');
            $absoluteCalendarInactiveImagesPath = new Path(__DIR__, 'View', 'images', 'calendar-windows', 'inactive', '*');

            $calendarActiveImages = array_map(function (string $path) use ($relativeCalendarActiveImagesFolderPath): string {
                $filename = substr($path, strripos($path, DIRECTORY_SEPARATOR) + 1);

                return $relativeCalendarActiveImagesFolderPath . $filename;
            }, glob($absoluteCalendarActiveImagesPath));

            $calendarInactiveImages = array_map(function (string $path) use ($relativeCalendarInactiveImagesFolderPath): string {
                $filename = substr($path, strripos($path, DIRECTORY_SEPARATOR) + 1);

                return $relativeCalendarInactiveImagesFolderPath . $filename;
            }, glob($absoluteCalendarInactiveImagesPath));

            $this->template->render('index.html.twig', [
                'user' => $user,
                'discordAvatarURL' => $discordAvatarURL,
                'calendarActiveImages' => $calendarActiveImages,
                'calendarInactiveImages' => $calendarInactiveImages,
                'daysLeft' => $daysLeft,
                'debug' => Request::getInstance()->has('debug'),
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
        $windowNumber = Request::getInstance()->getGet('day');
        $dayNumber = date('d');
        $isTodayWindow = $windowNumber !== null && $windowNumber === $dayNumber;
        $canOpenTodayWindow = CalendarOpenedWindow::canOpenTodayWindow($user, $dayNumber);

        if (Request::getInstance()->isUserLoggedIn()) {
            $reward = null;

            if ($isTodayWindow && $canOpenTodayWindow) {
                $reward = $this->handleReward($user, $dayNumber);
            }

            echo json_encode([
                'status' => 0,
                'reward' => $reward,
                'story' => CalendarStory::getDayStory($windowNumber),
            ]);
        } else {
            echo json_encode([
                'status' => 1,
            ]);
        }
    }

    private function handleReward(object $user, string $dayNumber): array {
        $reward = Reward::getInstance()->pickReward($user);
        CalendarOpenedWindow::openWindow($user->id, $dayNumber, $reward['label'], $reward['label'] === Reward::REWARD_LABEL_NITRO);

        if ($reward['label'] === Reward::REWARD_LABEL_TOKEN) {
            MemberToken::getInstance()->giveTokens($user->id, $reward['amount']);
        } else {
            $displayedLabels = $reward['label'] === Reward::REWARD_LABEL_NITRO
                ? [
                    'fr' => Reward::DISPLAYED_FR_LABEL_NITRO,
                    'en' => Reward::DISPLAYED_EN_LABEL_NITRO,
                ]
                : [
                    'fr' => Reward::DISPLAYED_FR_LABEL_PATREON,
                    'en' => Reward::DISPLAYED_EN_LABEL_PATREON,
                ];

            Reward::getInstance()->pingForSpecialReward($user->id, $displayedLabels);
        }

        return $reward;
    }
}