<?php
include "../Model/db.php";
session_start();

$datafile = "../data.json";  

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"] ?? "";
    $email = $_POST["email"] ?? "";
    $phone = $_POST["phone"] ?? "";
    $password = $_POST["password"] ?? "";

    $_SESSION["reg_old"] = array(
        "name" => $name,
        "email" => $email,
        "phone" => $phone
    );

    $errors = [];

    if (empty($name)) {
        $errors[] = "Name Required";
    }
   
    if (!str_contains($email, '@') || !str_contains($email, '.')) {
        $errors[] = "Invalid Email";
    }
    if (!is_numeric($phone)) {
        $errors[] = "Phone Must Be Numeric";
    }
    if (strlen($password) < 8) {
        $errors[] = "Password Must Be At Least 8 Characters";
    }

    if (!empty($errors)) {
        $_SESSION["reg_error"] = implode("<br>", $errors);
        header("Location: ../View/Registration.php");
        exit();
    }

    $database = new db();
    $connection = $database->connection();

    $result = $database->checkEmail($connection, $email);
    if ($result->num_rows > 0) {
        $_SESSION["reg_error"] = "Email Already Exists";
        header("Location: ../View/Registration.php");
        exit();
    }

    $password_hash = password_hash($password, PASSWORD_DEFAULT);


    $result = $database->registration($connection, $name, $email, $password_hash, $phone);

    if ($result) {
       
        $formdata = array(
            "name" => $name,
            "email" => $email,
            "phone" => $phone,
            "password_hash" => $password_hash, 
            "role" => "member"
        );

        if (file_exists($datafile)) {
            $existdata = file_get_contents($datafile);
            $tempdata = json_decode($existdata, true);
        } else {
            $tempdata = array();
        }
        if (!is_array($tempdata)) {
            $tempdata = array();
        }
        $tempdata[] = $formdata;
        $jsondata = json_encode($tempdata, JSON_PRETTY_PRINT);
        file_put_contents($datafile, $jsondata);
        
         $_SESSION["reg_success"] = true;
         
        unset($_SESSION["reg_error"]);
        unset($_SESSION["reg_old"]);
        header("Location: ../View/Login.php");
        exit();
    } else {
        $_SESSION["reg_error"] = "Registration Failed";
        header("Location: ../View/Registration.php");
        exit();
    }
}
?>