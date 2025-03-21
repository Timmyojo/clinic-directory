<?php

namespace App;
use PDO;

class Database {

    private ?PDO $conn = null;

    public function __construct(
        private string $host,
        private string $name,
        private string $user,
        private string $password,
    )
    {
        
    }

    function getConnection(): PDO {
        if ($this->conn === null) {
            $dsn = "mysql:host={$this->host};dbname={$this->name};charset=utf8";

            $this->conn = new PDO($dsn, $this->user, $this->password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);
        }
        return $this->conn;
    }
}