<?php

namespace Home\Entity;

use Befew\Db;
use Befew\Entity;
use PDO;

class CalendarOpenedWindow extends Entity
{
    private const TABLE_NAME = 'calendar_opened_window';

    private int $id;
    private string $userSnowflake;
    private int $dayNumber;

    public static function getAllForUser(string $userSnowflake): array {
        $query = Db::getInstance()->query(
            'SELECT * FROM ' . self::TABLE_NAME . ' WHERE user_snowflake = :userSnowflake ORDER BY day_number',
            ['userSnowflake' => $userSnowflake]
        );

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function openWindow(string $userSnowflake, int $dayNumber, string $rewardLabel, bool $isHighValue): void {
        Db::getInstance()->query(
            'INSERT INTO ' . self::TABLE_NAME . ' 
            (user_snowflake, day_number, reward_label, is_high_value_reward) 
            VALUES (:user_snowflake, :day_number, :reward_label, :is_high_value_reward)',
            [
                ':user_snowflake' => $userSnowflake,
                ':day_number' => $dayNumber,
                ':reward_label' => $rewardLabel,
                ':is_high_value_reward' => intval($isHighValue)
            ]
        );
    }

    public static function canOpenTodayWindow(object $user, int $day_number): bool {
        $query = Db::getInstance()->query(
            'SELECT * FROM ' . self::TABLE_NAME . ' 
            WHERE user_snowflake = :userId AND day_number = :day_number',
            [':userId' => $user->id, ':day_number' => $day_number]
        );

        $result = $query->fetch(PDO::FETCH_ASSOC);
        return !$result;
    }

    public static function alreadyHadHighValueReward(object $user): bool {
        $query = Db::getInstance()->query(
            'SELECT * FROM ' . self::TABLE_NAME . ' 
            WHERE user_snowflake = :userId AND is_high_value_reward = 1',
            [':userId' => $user->id]
        );

        $result = $query->fetch(PDO::FETCH_ASSOC);
        return !!$result;
    }
}