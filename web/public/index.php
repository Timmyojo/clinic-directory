<?php

declare(strict_types=1);


require dirname(__DIR__) . "/../vendor/autoload.php";

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . "/../../");
$dotenv->load();

$database = new App\Database($_ENV["DB_HOST"], $_ENV["DB_NAME"], $_ENV["DB_USER"], $_ENV["DB_PASSWORD"]);

$path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

$parts = explode("/", $path);

$uri = $parts[1];


$database = new App\Database($_ENV["DB_HOST"], $_ENV["DB_NAME"], $_ENV["DB_USER"], $_ENV["DB_PASSWORD"]);

$userModel = new Web\Model\UserModel($database);

if ($uri === "register") {
    $controller = new Web\Controller\User($userModel);
    $controller->register($_SERVER["REQUEST_METHOD"]);
    die();
} elseif ($uri === "logout") {
    $controller = new Web\Controller\User($userModel);
    $controller->logout($_SERVER["REQUEST_METHOD"]);
    die();
} else  {
    http_response_code(404);
    die();
}