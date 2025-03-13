<?php

declare(strict_types=1);


require dirname(__DIR__) . "/vendor/autoload.php";

header("Content-type: application/json; charset=UTF-8");

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

$parts = explode("/", $path);

$uri = $parts[2];

$id = $parts[3] ?? null;

$database = new App\Database($_ENV["DB_HOST"], $_ENV["DB_NAME"], $_ENV["DB_USER"], $_ENV["DB_PASSWORD"]);
$jwt = new App\JWTCodec;
$userModel = new Model\UserModel($database);
$practice = new Model\PracticeModel($database);
$apiAuth = new App\Authenticator($userModel, $jwt);
$refreshToken = new Model\RefreshTokenModel($database);

if ($uri === "authenticate") {
    $controller = new Controller\User($userModel, $refreshToken, $jwt);
    $controller->login($_SERVER["REQUEST_METHOD"]);
    die();
} elseif ($uri === "refresh") {
    $controller = new Controller\User($userModel, $refreshToken, $jwt);
    $controller->refresh($_SERVER["REQUEST_METHOD"]);
    die();
} elseif ($uri === "logout") {
    $controller = new Controller\User($userModel, $refreshToken, $jwt);
    $controller->logout($_SERVER["REQUEST_METHOD"]);
    die();
} elseif ($uri === "delete") {
    require(__DIR__ . "/remove_expired_refresh_tokens.php");
    die();
} elseif ($uri === "clinic") {
    

    if (!$apiAuth->authenticateAccessToken()) {
        die();
    }

    $user_id = $apiAuth->getUserId();
    $controller = new Controller\Practice($practice, $user_id);
    $controller->processRequest($_SERVER["REQUEST_METHOD"], $id);
    die();
} else  {
    http_response_code(404);
    die();
}