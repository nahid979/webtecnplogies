<?php
include "../Controller/auth_check.php";
auth_check("member");
include "../Model/db.php";

if (isset($_SESSION["profile_success"]) && $_SESSION["profile_success"] === true) {
    echo "<script>alert('Profile Updated Successfully!');</script>";
    unset($_SESSION["profile_success"]);
}
$error = $_SESSION["profile_error"] ?? "";
$old = $_SESSION["profile_old"] ?? array();
unset($_SESSION["profile_error"]);
unset($_SESSION["profile_old"]);

$database = new db();
$connection = $database->connection();
$id = $_SESSION["member_id"];
$result = $database->getMemberById($connection, $id);
$row = $result->fetch_assoc();

$name = $old["name"] ?? $row["name"];
$email = $old["email"] ?? $row["email"];
$phone = $old["phone"] ?? $row["phone"];
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Profile</title>
    <link rel="stylesheet" href="../Design/style.css">
</head>
<body>
    <header>
        <h1>Library Management System</h1>
    </header>
    <main>
        <fieldset>
            <legend></legend>
            <h2 style="text-align:center; margin-bottom:20px;">Update Profile</h2>
            <?php if ($error) echo "<div class='error-msg'>$error</div>"; ?>
            <form method="post" action="../Controller/ProfileController.php">
                <div class="form-group">
                    <label>Name:</label>
                    <input type="text" name="name" value="<?php echo $name; ?>" required>
                </div>
                <div class="form-group">
                    <label>Email:</label>
                    <input type="email" name="email" value="<?php echo $email; ?>" required>
                </div>
                <div class="form-group">
                    <label>Phone:</label>
                    <input type="text" name="phone" value="<?php echo $phone; ?>" required>
                </div>
                <div class="form-group">
                    <label>Current Password (for password change):</label>
                    <input type="password" name="current_password">
                </div>
                <div class="form-group">
                    <label>New Password (leave blank to keep same):</label>
                    <input type="password" name="new_password">
                </div>
                <input type="submit" value="Update" class="submit-btn">
            </form>
            <div class="dashboard-links">
                <a href="MemberDashboard.php">Back to Dashboard</a>
            </div>
        </fieldset>
    </main>
    <footer>
        <p>@ Library System</p>
    </footer>
</body>
</html>