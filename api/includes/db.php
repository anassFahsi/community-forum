<?php

use Pdo\Mysql;

function getPDO() {
    $db_host = getenv("DB_HOST");
    $db_port = getenv("DB_PORT");
    $db_name = getenv("DB_NAME");
    $db_user = getenv("DB_USER");
    $db_pass = getenv("DB_PASS");

    $ssl_ca = __DIR__ . "/../../certs/ca.pem";

    try {
        return new PDO(
            "mysql:host=$db_host;port=$db_port;dbname=$db_name;charset=utf8mb4",
            $db_user,
            $db_pass,
            [
                Mysql::ATTR_SSL_CA => $ssl_ca,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]
        );
    } catch (PDOException $e) {
        die("kunde inte ansluta till databasen:".$e->getMessage());
    }
}


