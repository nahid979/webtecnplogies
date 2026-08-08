<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function auth_check($required_role) {
    if (!isset($_SESSION["member_id"]) || !isset($_SESSION["role"]) || $_SESSION["role"] != $required_role) {
        header("Location: ../View/Login.php");
        exit();
    }
}
?>