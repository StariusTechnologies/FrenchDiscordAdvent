<?php

namespace Home\Entity;

use Befew\Db;
use Befew\Entity;
use PDO;

class Reward extends Entity
{
    private static ?Reward $instance = null;

    private const TABLE_NAME = 'calendar_special_reward';

    private int $id;
    private string $label;
    private int $gifted_quantity;

    public const REWARD_LABEL_NITRO = 'nitro';
    public const REWARD_LABEL_TOKEN = 'tokens';

    public const DISPLAYED_FR_LABEL_NITRO = 'un abonnement Nitro d\'un mois';
    public const DISPLAYED_EN_LABEL_NITRO = 'one month of Nitro';
    public const DISPLAYED_FR_LABEL_PATREON = 'un mois de Patreon French';
    public const DISPLAYED_EN_LABEL_PATREON = 'one month French Patreon';

    public static function getInstance(): Reward {
        if (self::$instance === null) {
            self::$instance = new Reward();
        }

        return self::$instance;
    }

    public function getRewardsInfo(?string $elem = null): array {
        $clauseWhere = '';
        $queryData = [];

        if ($elem !== null) {
            $clauseWhere = ' WHERE label = :label';
            $queryData[':label'] = $elem;
        }

        $query = Db::getInstance()->query(
            'SELECT * FROM ' . self::TABLE_NAME . $clauseWhere . ' ORDER BY label',
            $queryData
        );

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getNitroRewardsInfo(): array {
        $data = $this->getRewardsInfo(self::REWARD_LABEL_NITRO);
        return count($data) > 0 ? $data[0] : [];
    }

    public function pickReward(object $user, object $guildUser): array {
        $chanceForSpecialReward = 15;
        $specialRewardNumber = 7;
        $isSpecialReward = rand(0, $chanceForSpecialReward) === $specialRewardNumber;

        if ($isSpecialReward && MemberActivity::getInstance()->hasEnoughActivity($user->id)) {
            return $this->pickSpecialReward($user, $guildUser);
        } else {
            $minToken = 5;
            $maxToken = 10;

            return [
                'label' => 'tokens',
                'amount' => rand($minToken, $maxToken),
            ];
        }
    }

    public function pingForSpecialReward(string $userSnowflake, array $labels): void {
        $content = FR_EMOJI . "\n";
        $content .= '<@' . $userSnowflake . '> a gagn?? **' . $labels['fr'] . '** en ouvrant la fen??tre d\'aujourd\'hui sur le **Calendrier de l\'hiver** !';
        $content .= "\n" . '<@' . $userSnowflake . '>, Lily te contactera bient??t pour te donner ta r??compense !';
        $content .= "\n" . '**Toi aussi, tente ta chance** ! ici -> **https://winter.frenchdiscord.com**';
        $content .= "\n\n" . EN_EMOJI;
        $content .= "\n" . '<@' . $userSnowflake . '> opened today\'s window in the **Winter Calendar** and won **' . $labels['en'] . '**';
        $content .= "\n" . '<@' . $userSnowflake . '>, Lily will contact you shortly to give you your reward';
        $content .= "\n" . '**Take your chance too**! right here -> **https://winter.frenchdiscord.com**';

        DiscordAPI::getInstance()->postEventMessage($content);
    }

    private function __construct() {}

    private function pickSpecialReward(object $user, object $guildUser): array {
        $chanceForhighValueReward = 20;
        $highValueRewardNumber = 7;
        $ishighValueReward = rand(0, $chanceForhighValueReward) === $highValueRewardNumber;
        $reward = [];

        if ($this->stillHighValueRewardToGive() && !CalendarOpenedWindow::alreadyHadHighValueReward($user) && $ishighValueReward) {
            $reward = [
                'label' => 'nitro',
                'amount' => 1,
            ];
        } else {
            $possibleRewards = [
                ['label' => 'tokens', 'amount' => 50],
                ['label' => 'tokens', 'amount' => 50],
                ['label' => 'tokens', 'amount' => 50],
                ['label' => 'tokens', 'amount' => 50],
                ['label' => 'tokens', 'amount' => 75],
                ['label' => 'tokens', 'amount' => 75],
                ['label' => 'tokens', 'amount' => 75],
                ['label' => 'tokens', 'amount' => 100],
                ['label' => 'tokens', 'amount' => 100],
            ];

            if (!in_array(PATREON_ROLE_SNOWFLAKE, $guildUser->roles)) {
                $possibleRewards[] = ['label' => 'patreon', 'amount' => '1'];
            }

            $reward = $possibleRewards[rand(0, count($possibleRewards) - 1)];
        }

        $rewardLabel = $reward['label'] === 'tokens'
            ? $reward['amount'] . ' ' . $reward['label']
            : $reward['label'];

        $this->incrementGiftedValue($rewardLabel, $user);
        return $reward;
    }

    private function incrementGiftedValue(string $rewardLabel): void {
        $acceptedValues = ['nitro', 'patreon', '50 tokens', '75 tokens', '100 tokens'];

        if ($rewardLabel && in_array($rewardLabel, $acceptedValues)) {
            Db::getInstance()->query(
                'UPDATE ' . self::TABLE_NAME . ' 
                SET gifted_quantity = gifted_quantity + 1 
                WHERE label = :label',
                [':label' => $rewardLabel]
            );
        }
    }

    private function stillHighValueRewardToGive(): bool {
        $rewardsInfo = $this->getNitroRewardsInfo();
        $maxHighValueRewardGifted = 3;

        return $rewardsInfo && $rewardsInfo['gifted_quantity'] < $maxHighValueRewardGifted;
    }
}