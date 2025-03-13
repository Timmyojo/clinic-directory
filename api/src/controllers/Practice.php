<?php

namespace Controller;
use Model;

class Practice {

    function __construct(
        private Model\PracticeModel $practice,
        private int $user_id
        )
    {
        
    }

    public function processRequest(string $method, ?string $id): void {
        if ($id === null) {
            switch ($method) {
                case "GET":
                    echo json_encode($this->practice->findMany($this->user_id));
                    break;

                case "POST":
                    $payload = (array) json_decode(file_get_contents("php://input"), true);

                    $errors = $this->validateRequest($payload);

                    if (!empty($errors)) {
                        $this->respondUnproccessable($errors);
                        return;
                    }
                    
                    $id = $this->practice->create($payload, $this->user_id);
                    
                    http_response_code(201);
                    echo json_encode(["message" => "Task ID: {$id} created successfully"]);
                    break;
                
                default:
                    $this->methodNotAllowed("GET, POST");
                    break;
            }
        } else {
            $data = $this->practice->findOne($id, $this->user_id);
            if (!$data) {
                http_response_code(404);
                echo json_encode(["message" => "Task with id '{$id}' not found"]);
                return;
            }
            switch ($method) {
                case "GET":
                    
                    echo json_encode($data);
                    break;

                case "PATCH":
                    $payload = (array) json_decode(file_get_contents("php://input"), true);

                    $errors = $this->validateRequest($payload, false);

                    if (!empty($errors)) {
                        $this->respondUnproccessable($errors);
                        return;
                    }
                    
                    $id = $this->practice->update($payload, $this->user_id, $id);
                    echo json_encode(["message" => "Task ID: {$id} updated successfully"]);
                    break;
                    
                    break;
                    
                case "DELETE":
                    $id = $this->practice->delete($id, $this->user_id);
                    echo json_encode(["message" => "Task ID: {$id} deleted successfully"]);
                    break;
                
                default:
                    $this->methodNotAllowed("GET, PATCH, DELETE");
                    return;
                    break;
            }
        }
    }

    private function methodNotAllowed(string $allowedMethods): void {
        http_response_code(405);
        header("Allow: {$allowedMethods}");
    }


    private function respondUnproccessable(array $errors): void {
        http_response_code(422);
        echo json_encode(["errors" => $errors]);
    }

    private function validateRequest(array $payload, bool $new = true): array {
        $errors = [];
        if ($new && empty($payload["clinic_name"])) {
            $errors[] = "Name is required";
        }
        if ($new && empty($payload["owner"])) {
            $errors[] = "Owner is required";
        }
        if ($new && empty($payload["location"])) {
            $errors[] = "location is required";
        }

        return $errors;
    }


}