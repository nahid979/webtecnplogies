<?php
// config/database.php
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'bazar_hisab';
define('DB_PORT', 4306);

$conn = new mysqli($host, $user, $pass, $dbname, DB_PORT);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>