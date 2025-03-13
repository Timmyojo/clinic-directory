<?php
namespace Model;
use App;
use PDO;

class PracticeModel {
    private PDO $conn;

    public function __construct(App\Database $database)
    {
        $this->conn = $database->getConnection();
    }

    function findMany(int $user_id): array {
        
        $query = "SELECT * FROM clinic WHERE user_id = :user_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(":user_id", $user_id, PDO::PARAM_INT);

        $stmt->execute();

        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $data;
    }

    function findOne(string $id, int $user_id): array | false {
        
        $query = "SELECT * FROM clinic WHERE id = :id AND user_id = :user_id";

        $stmt = $this->conn->prepare($query);
        
        $stmt->bindValue(":id", $id, PDO::PARAM_STR);

        $stmt->bindValue(":user_id", $user_id, PDO::PARAM_INT);

        $stmt->execute();

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data;
    }

    function create(array $payload, int $user_id): string {
        
        $query = "INSERT INTO clinic (clinic_name, owner, location, user_id) VALUES (:clinic_name, :owner, :location, :user_id)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindValue(":clinic_name", $payload["clinic_name"], PDO::PARAM_STR);

        $stmt->bindValue(":owner", $payload["owner"], PDO::PARAM_STR);

        $stmt->bindValue(":location", $payload["location"], PDO::PARAM_STR);
        
        $stmt->bindValue(":user_id", $user_id, PDO::PARAM_INT);

        $stmt->execute();
        
        return $this->conn->lastInsertId();
    }

    function delete(string $id, int $user_id): string {
        
        $query = "DELETE FROM clinic WHERE id = :id AND user_id = :user_id";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindValue(":id", $id, PDO::PARAM_STR);

        $stmt->bindValue(":user_id", $user_id, PDO::PARAM_INT);

        $stmt->execute();
        
        return $stmt->rowCount();
    }

    function update(array $payload, int $user_id, string $id): string {

        $data = $this->findOne($id, $user_id);
        
        $query = "UPDATE clinic SET clinic_name = :clinic_name, owner = :owner, location = :location WHERE :id = id AND :user_id = user_id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindValue(":clinic_name", $payload["clinic_name"] ?? $data["clinic_name"], PDO::PARAM_STR);

        $stmt->bindValue(":owner", $payload["owner"] ?? $data["owner"], PDO::PARAM_STR);

        $stmt->bindValue(":location", $payload["location"] ?? $data["location"], PDO::PARAM_STR);

        $stmt->bindValue(":id", $id, PDO::PARAM_STR);

        $stmt->bindValue(":user_id", $user_id, PDO::PARAM_INT);

        $stmt->execute();
        
        return $this->conn->lastInsertId();
    }
}