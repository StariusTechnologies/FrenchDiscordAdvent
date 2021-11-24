<?php

namespace Befew;

use PDO;

class Db {
    protected static ?Db $instance = null;

    protected ?PDO $dbh = null;
    protected bool $isInitialised = false;

    public static function getInstance(): ?Db {
        if (self::$instance === null && DB_ACTIVE) {
            self::$instance = new Db();
        }

        if (DB_ACTIVE) {
            return self::$instance;
        } else {
            return null;
        }
    }

    public function init(bool $debug = true): void {
        try {
            $this->dbh = new PDO(
                DB_DRIVER . ':dbname=' . DB_NAME . ';host=' . DB_HOST . ';port=' . DB_PORT,
                DB_USER,
                DB_PASSWORD
            );
        } catch (\PDOException $e) {
            echo 'WARNING: Database connection error: ' . $e->getMessage();
        }

        $this->isInitialised = true;
        $this->dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $this->setDebugMode($debug);
    }

    public function getDBH(): PDO {
        return $this->dbh;
    }

    public function setDebugMode(bool $enabled = false): void {
        if ($enabled) {
            $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } else {
            $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
        }
    }

    public function query(string $query, array $queryData = []): ?\PDOStatement {
        $query = Db::getInstance()->getDBH()->prepare($query);
        $query->execute($queryData);
        return $query;
    }

    private function __construct() {
        if (DB_ACTIVE && !$this->isInitialised) {
            $this->init();
        }
    }
}