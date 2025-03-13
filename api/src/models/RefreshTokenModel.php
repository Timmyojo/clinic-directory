<?php

namespace Model;
use App;
use PDO;

class RefreshTokenModel {
    private PDO $conn;

    public function __construct(App\Database $database)
    {
        $this->conn = $database->getConnection();
    }




    function create(string $token, int $expires_at): bool {
        
        $query = "INSERT INTO refresh_token (token_hash, expires_at) VALUES (:token_hash, :expires_at)";

        $token_hash = hash_hmac(
            "sha256",
            $token,
            $_ENV["REFRESH_SECRET_KEY"]
        );

        $stmt = $this->conn->prepare($query);

        $stmt->bindValue(":token_hash", $token_hash, PDO::PARAM_STR);
        $stmt->bindValue(":expires_at", $expires_at, PDO::PARAM_INT);

        return $stmt->execute();
    }

    function delete(string $token):int {
        
        
        $token_hash = hash_hmac(
            "sha256",
            $token,
            $_ENV["REFRESH_SECRET_KEY"]
        );

        $query = "DELETE FROM refresh_token WHERE token_hash = :token_hash";

        $stmt = $this->conn->prepare($query);

        $stmt->bindValue(":token_hash", $token_hash, PDO::PARAM_STR);

        $stmt->execute();
        return $stmt->rowCount();
    }

    function findByToken(string $token): array | false {
        
        $token_hash = hash_hmac(
            "sha256",
            $token,
            $_ENV["REFRESH_SECRET_KEY"]
        );

        $query = "SELECT * FROM refresh_token WHERE token_hash = :token_hash";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(":token_hash", $token_hash, PDO::PARAM_STR);

        $stmt->execute();

        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $data;
    }

    function deleteExpired(): int {
        
        $query = "DELETE FROM refresh_token WHERE expires_at < UNIX_TIMESTAMP()";

        $stmt = $this->conn->query($query);
        
        return $stmt->rowCount();
    }
}