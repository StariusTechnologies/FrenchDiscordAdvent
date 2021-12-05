<?php

namespace Home\Entity;

use Befew\Db;
use Befew\Entity;

class MemberActivity extends Entity
{
    private static ?MemberActivity $instance = null;

    private const TABLE_VOCAL_NAME = 'stat_vocal';
    private const TABLE_MESSAGES_NAME = 'stat_messages';

    private const REQUIRED_MESSAGE_AMOUNT = 100;

    public static function getInstance(): MemberActivity {
        if (self::$instance === null) {
            self::$instance = new MemberActivity();
        }

        return self::$instance;
    }

    public function hasEnoughActivity(int $userSnowflake): bool {
        $messagesAmount = $this->getMessagesAmount($userSnowflake);
        $vocalTime = $this->getVocalAmount($userSnowflake);
        $calculatedMessagesAmount = $messagesAmount + ceil($vocalTime / 60 * 10);

        return $calculatedMessagesAmount >= self::REQUIRED_MESSAGE_AMOUNT;
    }

    private function __construct() {}

    private function getVocalAmount(int $userSnowflake): int {
        $query = Db::getInstance()->query(
            'SELECT SUM(data) AS amount 
            FROM ' . self::TABLE_VOCAL_NAME . ' 
            WHERE user_id = :userSnowflake',
            [':userSnowflake' => $userSnowflake]
        );

        $data = $query->fetch();

        if (empty($data['amount'])) {
            return 0;
        }

        return intval($data['amount']);
    }

    private function getMessagesAmount(int $userSnowflake): int {
        $query = Db::getInstance()->query(
            'SELECT SUM(data) AS amount 
            FROM ' . self::TABLE_MESSAGES_NAME . ' 
            WHERE user_id = :userSnowflake',
            [':userSnowflake' => $userSnowflake]
        );

        $data = $query->fetch();

        if (empty($data['amount'])) {
            return 0;
        }

        return intval($data['amount']);
    }
}