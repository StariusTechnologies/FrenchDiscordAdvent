<?php

namespace french\avent\model;
use french\avent\model\Database;

class AdventManager extends Database
{
    public static $TABLE_NAME = 'calendar_opened_window';

    public function getWindowState(string $userId)
    {
        $q = $this->sql(
            'SELECT * 
            FROM ' . static::$TABLE_NAME . ' 
            WHERE user_snowflake = :user_snowflake 
            ORDER BY day_number', 
            [':user_snowflake' => $userId]
        );

        $result = $q->fetchAll();
        $q->closeCursor();

        return $result;
    }
}
