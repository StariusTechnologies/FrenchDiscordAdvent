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
        $query = Db::getInstance()->getDBH()->prepare(
            'SELECT * FROM ' . self::TABLE_NAME . ' WHERE user_snowflake = :userSnowflake ORDER BY day_number',
            ['userSnowflake' => $userSnowflake]
        );

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
}