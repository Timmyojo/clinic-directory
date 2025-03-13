<?php

namespace Web\controller;
use Web\Model;

class User {

    function __construct(
        private Model\UserModel $userModel
        )
    {
        
    }

    public function register(string $method): void {
        switch ($method) {
            case 'GET':
                require __DIR__ . "/../views/register.php";
                return;
                break;
            case 'POST':
                $name = $_POST['name'];
                $username = $_POST['username'];
                $password = $_POST['password'];

                $errors = [];

                if (!isset($name)) {
                $errors['name'] = "Name is required.";
                }

                if (!isset($name)) {
                $errors['username'] = "Username is required.";
                }

                if (!isset($password)) {
                $errors['password'] = "Password is required.";
                }

                if (!empty($errors)) {
                    header("Location: /register");
                    die();
                }

                $user = $this->userModel->findByUsername($username);

                if ($user) {
                    $errors['email'] = "Email already used.";
                    header("Location: /register");
                    die();
                }
                $password_hash = password_hash($password, PASSWORD_BCRYPT);

                $api_key = bin2hex(random_bytes(16));

                $id = $this->userModel->create([
                    "name" => $name,
                    "username" => $username,
                    "password_hash" => $password_hash,
                    "api_key" => $api_key,
                ]);
                echo "You have successfully registerd. Your API key is {$api_key}";
                return;
                break;
            default:
                # code...
                break;
        }
    
    }

    public function logout(string $method): void {
        echo "Logout";
    
    }

}