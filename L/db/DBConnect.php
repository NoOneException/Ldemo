<?php

namespace L\db;

use L\base\Component;
use PDO;

final class DBConnect implements Component
{
    public $dsn;
    public $username;
    public $password;
    public $tablePrefix;
    private $transactions = [];
    private $commands = [];
    private $sql = [];
    private $lastSql = [];

    private static $pdos = [];

    public function init()
    {
    }

    /**
     * @return PDO
     */
    public function getPdo(): PDO
    {
        $config = $this->getConnect();
        $key = $config['dsn'] . '-' . $config['username'] . '-' . $config['password'];
        if (!isset(self::$pdos[$key])) {
            $pdo = new PDO($config['dsn'], $config['username'], $config['password']);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            $pdo->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, true);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $pdo->exec('set names utf8mb4');
            self::$pdos[$key] = $pdo;
        }
        return self::$pdos[$key];
    }

    public function getCommand(): DBCommand
    {
        $key = $this->getConnectKey();
        if (!isset($this->commands[$key])) {
            $this->commands[$key] = new DBCommand($this->getPdo());
        }
        return $this->commands[$key];
    }

    public function getConnect(): array
    {
        return ['dsn' => $this->dsn, 'username' => $this->username, 'password' => $this->password,];
    }

    public function beginTransaction(): Transaction
    {
        $connect = $this->getPdo();
        $connect->beginTransaction();
        $transaction = new Transaction($connect);
        $this->transactions[] = $transaction;
        return $transaction;
    }

    public function close()
    {
        self::$pdos = [];
        foreach ($this->transactions as $transaction) {
            $transaction->close();
        }
    }

    public function getAllSql(): array
    {
        $key = $this->getConnectKey();
        return $this->sql[$key];
    }

    public function getLastSql(): DBSql
    {
        $key = $this->getConnectKey();
        return $this->lastSql[$key];
    }

    public function addSql(DBSql $sql)
    {
        $key = $this->getConnectKey();
        $this->lastSql[$key] = $sql;
        $this->sql[$key][] = $sql;
    }

    /**
     * @return string
     */
    private function getConnectKey(): string
    {
        $config = $this->getConnect();
        $key = $config['dsn'] . '-' . $config['username'] . '-' . $config['password'];
        return $key;
    }

    /**
     * @param string $dsn
     * @return $this
     */
    public function setDsn(string $dsn)
    {
        $this->dsn = $dsn;
        return $this;
    }

    /**
     * @param string $username
     * @return $this
     */
    public function setUsername(string $username)
    {
        $this->username = $username;
        return $this;
    }

    /**
     * @param string $password
     * @return $this
     */
    public function setPassword(string $password)
    {
        $this->password = $password;
        return $this;
    }
}
