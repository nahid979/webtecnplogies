<?php
class db {
    function connection() {
        $db_host = "localhost";
        $db_user = "root";
        $db_password = "";
        $db_name = "library_management_system";

        $connection = new mysqli($db_host, $db_user, $db_password, $db_name, 4306);
        if ($connection->connect_error) {
            die("Could not Connect Database: " . $connection->connect_error);
        }
        return $connection;
    }

    function checkEmail($connection, $email) {
        $sql = "SELECT id FROM members WHERE email = ?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        return $stmt->get_result();
    }

    function checkEmailExceptId($connection, $email, $id) {
        $sql = "SELECT id FROM members WHERE email = ? AND id != ?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("si", $email, $id);
        $stmt->execute();
        return $stmt->get_result();
    }

    function registration($connection, $name, $email, $password_hash, $phone) {
        $sql = "INSERT INTO members (name, email, password_hash, phone, role) VALUES (?, ?, ?, ?, 'member')";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("ssss", $name, $email, $password_hash, $phone);
        return $stmt->execute();
    }

    function login($connection, $email) {
        $sql = "SELECT id, name, role, password_hash FROM members WHERE email = ?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        return $stmt->get_result();
    }

    function getMemberById($connection, $id) {
        $sql = "SELECT name, email, phone, password_hash FROM members WHERE id = ?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result();
    }

    function updateProfile($connection, $id, $name, $email, $phone) {
        $sql = "UPDATE members SET name=?, email=?, phone=? WHERE id=?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("sssi", $name, $email, $phone, $id);
        return $stmt->execute();
    }

    function updatePassword($connection, $id, $password_hash) {
        $sql = "UPDATE members SET password_hash=? WHERE id=?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("si", $password_hash, $id);
        return $stmt->execute();
    }
}
?>