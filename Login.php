<?php
session_start();

if (isset($_SESSION["reg_success"]) && $_SESSION["reg_success"] === true) {
    echo "<script>alert('Registration Successful! Please login.');</script>";
    unset($_SESSION["reg_success"]);
}


if (isset($_SESSION["member_id"])) {
    $role = $_SESSION["role"];
    if ($role == "member") header("Location: MemberDashboard.php");
    elseif ($role == "librarian") header("Location: LibrarianDashboard.php");
    else header("Location: AdminDashboard.php");
    exit();
}

$error = $_SESSION["login_error"] ?? "";
$old = $_SESSION["login_old"] ?? array();
unset($_SESSION["login_error"]);
unset($_SESSION["login_old"]);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="../Design/style.css">
</head>
<body>
    <header>
        <h1>Library Management System</h1>
    </header>
    <main>
        <fieldset>
            <legend></legend>
            <h2 style="text-align:center; margin-bottom:20px;">Login</h2>
            <?php if ($error) echo "<div class='error-msg'>$error</div>"; ?>
            <form method="post" action="../Controller/LoginValidation.php">
                <div class="form-group">
                    <label>Email:</label>
                    <input type="email" name="email" value="<?php echo $old['email'] ?? ''; ?>" required>
                </div>
                <div class="form-group">
                    <label>Password:</label>
                    <input type="password" name="password" required>
                </div>
                <input type="submit" value="Login" class="submit-btn">
            </form>
            <div style="text-align: center; margin-top: 15px;">
                <a href="Registration.php">Don't have an account? Register here</a>
            </div>
        </fieldset>
    </main>
    <footer>
        <p>@ Library System</p>
    </footer>
</body>
</html>