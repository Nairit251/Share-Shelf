<?php
// ---------------------------------------------------------------
// Database connection for Share Shelf (XAMPP / MySQL / MariaDB)
// ---------------------------------------------------------------
$DB_HOST = "localhost";
$DB_USER = "root";
$DB_PASS = "";
$DB_NAME = "share_shelf";

$conn = mysqli_connect($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");
