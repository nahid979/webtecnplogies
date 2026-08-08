<?php
session_start();
$error = $_SESSION["reg_error"] ?? "";
$old = $_SESSION["reg_old"] ?? array();
unset($_SESSION["reg_error"]);
unset($_SESSION["reg_old"]);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Registration</title>
    <link rel="stylesheet" href="../Design/style.css">
    <script src="../Controller/JS/CheckEmail.js"></script>
</head>
<body>
    <header>
        <h1>Library Management System</h1>
    </header>
    <main>
        <fieldset>
            <legend></legend>
            <h2 style="text-align:center; margin-bottom:20px;">Member Registration</h2>
            <?php if ($error) echo "<div class='error-msg'>$error</div>"; ?>
            <form method="post" action="../Controller/RegistrationController.php">
                <div class="form-group">
                    <label>Name:</label>
                    <input type="text" name="name" value="<?php echo $old['name'] ?? ''; ?>" required>
                </div>
                <div class="form-group">
                    <label>Email:</label>
                    <input type="email" id="email" name="email" onkeyup="CheckEmail()" value="<?php echo $old['email'] ?? ''; ?>" required>
                    <div id="emailresponse"></div>
                </div>
                <div class="form-group">
                    <label>Phone:</label>
                    <input type="text" name="phone" value="<?php echo $old['phone'] ?? ''; ?>" required>
                </div>
                <div class="form-group">
                    <label>Password (min 8 chars):</label>
                    <input type="password" name="password" required>
                </div>
                <input type="submit" value="Register" class="submit-btn">
            </form>
        </fieldset>
    </main>
    <footer>
        <p>@ Library System</p>
    </footer>
</body>
</html>