<?php

namespace L\db;

use PDO;

class DBConnect
{
    public $dsn;
    public $username;
    public $password;
    private $pdos = [];
    private $transactions = [];

    /**
     * @return PDO
     */
    public function getPdo(): PDO
    {
        $config = $this->getConnect();
        $key = $config['dsn'] . '-' . $config['username'] . '-' . $config['password'];
        if (!isset($this->pdos[$key])) {
            $pdo = new PDO($config['dsn'], $config['username'], $config['password']);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            $pdo->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, true);
            $pdo->exec('set names utf8mb4');
            $this->pdos[$key] = $pdo;
        }
        return $this->pdos[$key];
    }
//    public static $flagConnectListenerInterfaceArr = array();
//    public function addConnectListener(ConnectListenerInterface $connectListenerInterface, $flag = 'default')
//    {
//        self::$flagConnectListenerInterfaceArr[$flag][] = $connectListenerInterface;
//    }
//
//    public function clearConnectListener($flag)
//    {
//        self::$flagConnectListenerInterfaceArr[$flag] = array();
//    }

    public function getConnect()
    {
//        /**
//         * @var $connectListenerInterface ConnectListenerInterface
//         */
//        foreach (self::$flagConnectListenerInterfaceArr as $connectListenerInterfaces) {
//            foreach ($connectListenerInterfaces as $connectListenerInterface) {
//                $r = $connectListenerInterface->onConnect();
//                if($r){
//                    return $r;
//                }
//            }
//        }
        return ['dsn' => $this->dsn, 'username' => $this->username, 'password' => $this->password,];
    }

//    public function beginTransaction()
//    {
//        $connect = $this->getPdo();
//        $connect->beginTransaction();
//        $obj = new CTransaction($connect);
//        $this->transactions[] = $obj;
//        return $obj;
//    }

//    public function getLock()
//    {
//        \CC\db\base\select\ItemModel::make('lock_table')->addForUpdate()->execute();
//    }
    public function close()
    {
        $this->pdos = [];
        foreach ($this->transactions as $transaction) {
            $transaction->close();
        }
    }
}
