<?php
session_start();
include "../Model/db.php";
include "auth_check.php";

auth_check("member");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
        $name = $_POST["name"] ?? "";
    
           $email = $_POST["email"] ?? "";
               $phone = $_POST["phone"] ?? "";
    $current_password = $_POST["current_password"] ?? "";
                 $new_password = $_POST["new_password"] ?? "";

    
                 $_SESSION["profile_old"] = array(
        "name" => $name,
        "email" => $email,
        "phone" => $phone
    );


    if (empty($name)) {
        $_SESSION["profile_error"] = "Name Required";
        header("Location: ../View/Profile.php");
        exit();
    }
    if (!str_contains($email, '@') || !str_contains($email, '.')) {
        $_SESSION["profile_error"] = "Invalid Email";
        header("Location: ../View/Profile.php");
        exit();
    }
    if (!is_numeric($phone)) {
        $_SESSION["profile_error"] = "Phone Must Be Numeric";
        header("Location: ../View/Profile.php");
        exit();
    }
    

    $database = new db();
    $connection = $database->connection();
    $id = $_SESSION["member_id"];

    $memberResult = $database->getMemberById($connection, $id);
    $memberRow = $memberResult->fetch_assoc();

    $emailCheck = $database->checkEmailExceptId($connection, $email, $id);
    if ($emailCheck->num_rows > 0) {
        $_SESSION["profile_error"] = "Email Already Exists";
        header("Location: ../View/Profile.php");
        exit();
    }

    if (!empty($new_password)) {
        if (empty($current_password)) {
            $_SESSION["profile_error"] = "Current Password Required";
            header("Location: ../View/Profile.php");
            exit();
        }
        if (!password_verify($current_password, $memberRow["password_hash"])) {
            $_SESSION["profile_error"] = "Current Password Wrong";
            header("Location: ../View/Profile.php");
            exit();
        }
        if (strlen($new_password) < 8) {
            $_SESSION["profile_error"] = "New Password Must Be At Least 8 Characters";
            header("Location: ../View/Profile.php");
            exit();
        }
        $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
        $passResult = $database->updatePassword($connection, $id, $new_hash);
        if (!$passResult) {
            $_SESSION["profile_error"] = "Password Update Failed";
            header("Location: ../View/Profile.php");
            exit();
        }
    }

    $result = $database->updateProfile($connection, $id, $name, $email, $phone);
    if ($result) {
        $_SESSION["name"] = $name;
          $_SESSION["profile_success"] = true;
        unset($_SESSION["profile_error"]);
        unset($_SESSION["profile_old"]);
        header("Location: ../View/Profile.php");
        exit();
    } else {
        $_SESSION["profile_error"] = "Profile Update Failed";
        header("Location: ../View/Profile.php");
        exit();
    }

    
}
?>