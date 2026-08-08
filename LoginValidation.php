<?php
        
        
        include "../Model/db.php";

        session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = $_POST["email"] ?? "";
    $password = $_POST["password"] ?? "";


    $_SESSION["login_old"] = array("email" => $email);

    if (empty($email) || empty($password)) {

        $_SESSION["login_error"] = "Please Fill All Fields";
        header("Location: ../View/Login.php");

        exit();
    }

    $database = new db();
    $connection = $database->connection();
    $result = $database->login($connection, $email);

    if ($result->num_rows == 1) {

        $row = $result->fetch_assoc();

        if (password_verify($password, $row["password_hash"])) {
            $_SESSION["member_id"] = $row["id"];
            $_SESSION["name"] = $row["name"];
            $_SESSION["role"] = $row["role"];

            unset($_SESSION["login_error"]);
            unset($_SESSION["login_old"]);




            if ($row["role"] == "member") {
                             
            header("Location: ../View/MemberDashboard.php");
            } elseif ($row["role"] == "librarian") {
                         
            header("Location: ../View/LibrarianDashboard.php");
            } else {
                     
            header("Location: ../View/AdminDashboard.php");
            }
            exit();


        } else {



            $_SESSION["login_error"] = "Wrong Password";
            header("Location: ../View/Login.php");
            exit();
        }
    } else {
        $_SESSION["login_error"] = "Email Not Found";
        header("Location: ../View/Login.php");
        exit();
    }
}
?>