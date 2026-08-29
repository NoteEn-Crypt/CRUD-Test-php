<?php
    // PostgreSQL server configuration
    $server = "localhost";
    $name = "postgres";
    $password = "chien";
    $db = "members";

    // Create database connection
    try {
        $conn = new PDO("pgsql:host=$server;dbname=$db", $name, $password);
        $conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("Error connecting to PostgreSQL Server: " .$e->getMessage());
    }
?>