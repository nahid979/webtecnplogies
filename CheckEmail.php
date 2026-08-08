<?php
include "../Model/db.php";

$email = $_POST["email"] ?? "";

if (!$email) {
    echo "Email Required!";
    exit();
}

$database = new db();
$connection = $database->connection();
$result = $database->checkEmail($connection, $email);

if ($result->num_rows > 0) {
    echo "Email Already Taken!";
} else {
    echo "Email Available";
}
?>