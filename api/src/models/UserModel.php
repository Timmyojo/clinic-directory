<?php

namespace Model;
use App;
use PDO;

class UserModel {
    private PDO $conn;

    public function __construct(App\Database $database)
    {
        $this->conn = $database->getConnection();
    }

   

    function findByApiKey(string $api_key): array | false {
        
        $query = "SELECT * FROM user WHERE api_key = :api_key";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(":api_key", $api_key, PDO::PARAM_STR);

        $stmt->execute();

        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $data;
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

   
}