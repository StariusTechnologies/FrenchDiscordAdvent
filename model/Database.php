<?php

namespace french\avent\model;

abstract class Database
{
    protected static $db;
    
    const ERRMODE = array(\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION);

    protected function getConnection()
    {
        if (empty(self::$db)) {
            global $DB_HOST, $DB_LOGIN, $DB_NAME, $DB_PASSWORD;

            $dbData = 'mysql:host=' . $DB_HOST . ';dbname=' . $DB_NAME . ';charset=utf8';
            self::$db = new \PDO($dbData, $DB_LOGIN, $DB_PASSWORD, self::ERRMODE);
        }

        return self::$db;
    }

    public function sql($req, array $parameters = null)
    {
        $q = $this->getConnection()->prepare($req);

        if ($parameters) {
            foreach ($parameters as $paraKey => $paraValue) {
                if (is_int($paraValue)) {
                    $q->bindValue($paraKey, $paraValue, \PDO::PARAM_INT);
                } else {//is string
                    $q->bindValue($paraKey, $paraValue);
                }
            }
        }

        $q->execute();

        return $q;
    }

    public function getLastInsertId()
    {
        $q = $this->getConnection();
        return $q->lastInsertId();
    }
}
