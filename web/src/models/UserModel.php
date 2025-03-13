<?php

namespace Web\Model;
use App;
use PDO;

class UserModel {
    private PDO $conn;

    public function __construct(App\Database $database)
    {
        $this->conn = $database->getConnection();
    }

   

    function findByUsername(string $username): array | false {
        
        $query = "SELECT * FROM user WHERE username = :username";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(":username", $username, PDO::PARAM_STR);

        $stmt->execute();

        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $data;
    }

    function findByID(int $user_id): array | false {
        
        $query = "SELECT * FROM user WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(":id", $user_id, PDO::PARAM_INT);

        $stmt->execute();

        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $data;
    }

    function create(array $payload): string {
        
        $query = "INSERT INTO user (name, username, password_hash, api_key) VALUES (:name, :username, :password_hash, :api_key)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindValue(":name", $payload["name"], PDO::PARAM_STR);

        $stmt->bindValue(":username", $payload["username"], PDO::PARAM_STR);

        $stmt->bindValue(":password_hash", $payload["password_hash"], PDO::PARAM_STR);
        
        $stmt->bindValue(":api_key", $payload["api_key"], PDO::PARAM_STR);

        $stmt->execute();
        
        return $this->conn->lastInsertId();
    }

   
}