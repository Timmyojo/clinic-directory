<?php

namespace Controller;
use Model;
use App;
use Exceptions;

class User {

    function __construct(
        private Model\UserModel $userModel,
        private Model\RefreshTokenModel $refreshToken,
        private App\JWTCodec $jwt
        )
    {
        
    }

    public function login(string $method): void {

        switch ($method) {
            case 'POST':

                if (empty($_SERVER["HTTP_X_API_KEY"])) {
                    http_response_code(400);
                    echo json_encode(["message" => "missing api key"]);
                    die();
                }

                $api_key = $_SERVER["HTTP_X_API_KEY"];

                $user = $this->userModel->findByApiKey($api_key);

                if (!$user) {
                    http_response_code(401);
                    echo json_encode(["message" => "Invalid api key"]);
                    die();
                }


                $payload = [
                    "sub" => $user["id"],
                    "name" => $user["name"],
                    "exp" => time() + (int) $_ENV["ACCESS_TOKEN_EXPIRY"]
                ];

                $access_token = $this->jwt->encode(
                    $payload,
                    $_ENV["ACCESS_SECRET_KEY"]
                );

                $refresh_token_expiry = time() + (int) $_ENV["REFRESH_TOKEN_EXPIRY"];

                $refresh_token = $this->jwt->encode(
                    [
                    "sub" => $user["id"],
                    "exp" => $refresh_token_expiry
                    ],
                    $_ENV["REFRESH_SECRET_KEY"]
                );

                echo json_encode([
                    "access_token" => $access_token,
                    "refresh_token" => $refresh_token
                ]);

                $this->refreshToken->create($refresh_token, $refresh_token_expiry);
                return;
                break;
            
            default:
                $this->methodNotAllowed("POST");
                return;
                break;
        }
    
    }

    public function refresh(string $method): void {

        switch ($method) {
            case 'POST':

                $data = (array) json_decode(file_get_contents("php://input"), true);

                if (!array_key_exists("token", $data)) {
                    http_response_code(400);
                    echo json_encode(["message" => "Missing refresh token"]);
                    die();
                }

                $token = $data["token"];

                try {
                    $payload = $this->jwt->decode(
                        $token,
                        $_ENV["REFRESH_SECRET_KEY"]
                    );
                } catch (Exceptions\TokenExpiredException) {
                    http_response_code(401);
                    echo json_encode(["message" => "token has expired"]);
                    die();
                } catch (\Exception) {
                    http_response_code(400);
                    echo json_encode(["message" => "invalid token"]);
                    die();
                }

                $refresh_token = $this->refreshToken->findByToken($data["token"]);
                
                if (!$refresh_token) {
                    http_response_code(401);
                    echo json_encode(["message" => "Invalid token (not on whitelist)"]);
                    die();
                }

                $user_id = $payload["sub"];

                $user = $this->userModel->findByID($user_id);
                if (!$user) {
                    http_response_code(401);
                    echo json_encode(["message" => "Invalid authentication"]);
                    die();
                }

                $payload = [
                    "sub" => $user["id"],
                    "name" => $user["name"],
                    "exp" => time() + (int) $_ENV["ACCESS_TOKEN_EXPIRY"]
                ];
                
                $access_token = $this->jwt->encode(
                    $payload,
                    $_ENV["ACCESS_SECRET_KEY"]
                );
                
                echo json_encode([
                    "access_token" => $access_token
                ]);
                return;
                break;
            
            default:
                $this->methodNotAllowed("POST");
                return;
                break;
        }
    
    }

    public function logout(string $method): void {

        switch ($method) {
            case 'POST':

                $data = (array) json_decode(file_get_contents("php://input"), true);

                if (!array_key_exists("token", $data)) {
                    http_response_code(400);
                    echo json_encode(["message" => "Missing refresh token"]);
                    die();
                }

                $token = $data["token"];



                try {
                    $payload = $this->jwt->decode(
                        $token,
                        $_ENV["REFRESH_SECRET_KEY"]
                    );
                } catch (Exceptions\TokenExpiredException) {
                    http_response_code(401);
                    echo json_encode(["message" => "token has expired"]);
                    die();
                } catch (\Exception) {
                    http_response_code(400);
                    echo json_encode(["message" => "invalid token"]);
                    die();
                }
                $this->refreshToken->delete($data["token"]);
                return;
                break;
            
            default:
                $this->methodNotAllowed("POST");
                return;
                break;
        }
    
    }

    private function methodNotAllowed(string $allowedMethods): void {
        http_response_code(405);
        header("Allow: {$allowedMethods}");
    }

}