<?php

require_once __DIR__ . '/config.php';

if (!DB_HOST || !DB_NAME || !DB_USER || DB_PASS === null) {
    die("Database configuration is missing");
}

$conn = mysqli_connect(
    DB_HOST,
    DB_USER,
    DB_PASS,
    DB_NAME,
    DB_PORT
);

if (!$conn) {
    die("Connection Failed: " . mysqli_connect_error());
}