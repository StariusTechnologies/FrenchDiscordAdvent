<?php

namespace Home\Entity;

use Befew\Db;
use Befew\Entity;
use PDO;

class Reward extends Entity
{
    private const TABLE_NAME = 'calendar_special_reward';

    private int $id;
    private string $label;
    private int $gifted_quantity;

    public static function getRewardsInfo(): array {
        $query = Db::getInstance()->query(
            'SELECT * FROM ' . self::TABLE_NAME . ' ORDER BY label DESC'
        );

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function pickReward(object $user): array {
        $chanceForSpecialReward = 15;
        $specialRewardNumber = 7;
        $isSpecialReward = rand(0, $chanceForSpecialReward) === $specialRewardNumber;

        if ($isSpecialReward) {
            return self::pickSpecialReward($user);
        } else {
            $minToken = 5;
            $maxToken = 10;

            return [
                'name' => 'tokens',
                'amount' => rand($minToken, $maxToken),
            ];
        }
    }

    private static function pickSpecialReward(object $user): array {
        $chanceForhighValueReward = 10;
        $highValueRewardNumber = 7;
        $ishighValueReward = rand(0, $chanceForhighValueReward) === $highValueRewardNumber;
        $reward = [];

        if (self::stillHighValueRewardToGive() && !self::alreadyHadHighValueReward($user) && $ishighValueReward) {
            $reward = [
                'label' => 'nitro',
                'amount' => 1,
            ];
        } else {
            $possibleRewards = [
                ['label' => 'tokens', 'amount' => '50'],
                ['label' => 'tokens', 'amount' => '75'],
                ['label' => 'tokens', 'amount' => '100'],
            ];

            //TODO if don't have patreon, reward .push ['label' => 'patreon', 'amount' => '1']

            $reward =  $possibleRewards[rand(0, count($possibleRewards) - 1)];
        }

        $rewardLabel = $reward['label'] === 'tokens'
            ? $reward['amount'] + ' ' + $reward['label']
            : $reward['label'];

        self::incrementGiftedValue($rewardLabel, $user);
        return $reward;
    }

    private static function incrementGiftedValue(string $rewardLabel, object $user): void {
        $acceptedValues = ['nitro', 'patreon', '50 token', '75 token', '100 token'];

        if ($rewardLabel && in_array($rewardLabel, $acceptedValues)) {
            Db::getInstance()->query(
                'UPDATE ' . self::TABLE_NAME . ' 
                SET gifted_quantity = gifted_quantity + 1 
                WHERE label = :label AND user_snowflake = :user_snowflake',
                [':user_snowflake' => $user->id, ':label' => $rewardLabel]
            );
        }
    }

    private static function stillHighValueRewardToGive(): bool {
        $rewardsInfo = self::getRewardsInfo();
        $maxHighValueRewardGifted = 3;

        return $rewardsInfo && $rewardsInfo['nitro'] && $rewardsInfo['nitro']['gifted_quantity'] < $maxHighValueRewardGifted;
    }

    private static function alreadyHadHighValueReward(object $user): bool {
        $query = Db::getInstance()->query(
            'SELECT * FROM ' . self::TABLE_NAME . ' 
            WHERE user_snowflake = :userId AND is_high_value_reward = 1',
            [':userId' => $user->id]
        );

        $result = $query->fetch(PDO::FETCH_ASSOC);
        return count($result) > 0;
    }
}