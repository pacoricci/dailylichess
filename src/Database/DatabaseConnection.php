<?php

namespace Src\Database;

use PDO;

class DatabaseConnection
{
    public $database;

    public function connect($password): PDO
    {
        if (!$this->database) {
            $hostname = "localhost";
            $username = "dailylichess";
            $database = "dailylichessDB";

            $options = [PDO::ATTR_PERSISTENT => true];

            $this->database = new PDO(
                "mysql:host=$hostname;dbname=$database;charset=utf8",
                $username,
                $password,
                $options
            );

            return $this->database;
        }
    }

    public function disconnect()
    {
        $this->database = null;
    }
}
