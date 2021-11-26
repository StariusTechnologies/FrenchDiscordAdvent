<?php

namespace Home\Entity;

use Befew\Db;
use Befew\Entity;
use PDO;

class MemberToken extends Entity
{
    private static ?MemberToken $instance = null;

    private const TABLE_NAME = 'member_token_info';

    private string $user_id;
    private int $amount;
    private int $all_time_amount;
    private int $amount_ticket;

    public static function getInstance(): MemberToken {
        if (self::$instance === null) {
            self::$instance = new MemberToken();
        }

        return self::$instance;
    }

    public function giveTokens(int $userSnowflake, int $amount, bool $addToAllTimeAmount = true): void {
        $membersTokenInfo = $this->getMemberTokenInfo($userSnowflake);

        if (!$membersTokenInfo) {
            $this->createMemberTokenInfo($userSnowflake, $amount);
        } else {
            $newCurrentAmount = $membersTokenInfo[0]['amount'] + $amount;
            $newAllTimeAmount = $addToAllTimeAmount ? $membersTokenInfo[0]['all_time_amount'] + $amount : $membersTokenInfo[0]['all_time_amount'];

            Db::getInstance()->query(
                'UPDATE ' . self::TABLE_NAME . '
                SET amount = :amout, all_time_amount = :all_time_amount
                WHERE user_id = :userSnowflake',
                [':amout' => $newCurrentAmount, ':all_time_amount' => $newAllTimeAmount, ':userSnowflake' => $userSnowflake]
            );
        }
    }

    private function getMemberTokenInfo(string $userSnowflake): array {
        $query = Db::getInstance()->query(
            'SELECT user_id, amount, all_time_amount, amount_ticket FROM ' . self::TABLE_NAME . ' WHERE user_id = :userSnowflake',
            [':userSnowflake' => $userSnowflake]
        );

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    private function createMemberTokenInfo(string $userSnowflake, int $amount = 1): void {
        Db::getInstance()->query(
            'INSERT INTO ' . self::TABLE_NAME . ' (user_id, amount, all_time_amount) VALUES (:userSnowflake, :amount, :all_time_amount)',
            [':userSnowflake' => $userSnowflake, ':amount' => $amount, ':all_time_amount' => $amount]
        );
    }

    private function __construct() {}
}