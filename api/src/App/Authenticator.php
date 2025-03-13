<?php

namespace App;
use Model;
use Exceptions;

class Authenticator {

    private $user_id;

    public function __construct(private Model\UserModel $userModel,
                                private JWTCodec $jwt
    )
    {
        
    }

    public function authenticateAccessToken() : bool {
        if (!preg_match("/^Bearer\s(.*)$/", $_SERVER["HTTP_AUTHORIZATION"], $matches)) {
            http_response_code(400);
            echo json_encode(["message" => "incomplete authorization header"]);
            return false;
        }

        $token = $matches[1];

        try {
            $data = $this->jwt->decode(
                $token,
                "nalMtVuu9z/oLz6HuBa7tieCsg6c4pGtSuTvDED0rU82vA9ny0owEELLhEwlUvrQ"
            );
        } catch (Exceptions\InvalidSignatureException) {
            http_response_code(401);
            echo json_encode(["message" => "Invalid signature"]);
            return false;
        } catch (Exceptions\TokenExpiredException) {
            http_response_code(401);
            echo json_encode(["message" => "Token Expired"]);
            return false;
        } catch (\Exception $e) {
            http_response_code(400);
            echo json_encode(["message" => $e->getMessage()]);
            return false; 
        }

        $this->user_id = $data["sub"];     
        return true;
    }

    public function getUserId() : int {      
        return $this->user_id;
    }
}