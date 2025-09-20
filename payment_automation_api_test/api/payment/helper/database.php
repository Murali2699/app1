<?php
require __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();
$hostname = $_ENV['DB_HOST'];
$database = $_ENV['DB_DATABASE'];
$username = $_ENV['DB_USERNAME'];
$password = $_ENV['DB_PASSWORD'];
$port = $_ENV['DB_PORT'];
try {
    $db = new PDO("pgsql:host=$hostname;port=$port;dbname=$database", $username, $password);
} catch (PDOException $e) {
    print_r($e);
    die('DB connection error');
}
?>