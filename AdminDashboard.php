<?php
include "../Controller/auth_check.php";
auth_check("admin");
$name = $_SESSION["name"] ?? "";
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel</title>
    <link rel="stylesheet" href="../Design/style.css">
</head>
<body>
    <header>
        <h1>Admin Panel</h1>
    </header>
    <main>
        <fieldset>
            <legend>Welcome Admin <?php echo $name; ?></legend>
            <div class="dashboard-links">
                <a href="admin/report.php">📊 Borrowing Trends Report</a><br><br>
                <a href="../Controller/Logout.php">🚪 Logout</a>
            </div>
        </fieldset>
    </main>
    <footer><p>&copy; Library System</p></footer>
</body>
</html>