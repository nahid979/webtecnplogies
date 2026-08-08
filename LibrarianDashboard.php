<?php
include "../Controller/auth_check.php";
auth_check("librarian");
$name = $_SESSION["name"] ?? "";
?>
<!DOCTYPE html>
<html>
<head>
    <title>Librarian Panel</title>
    <link rel="stylesheet" href="../Design/style.css">
</head>
<body>
    <header>
        <h1>Librarian Panel</h1>
    </header>
    <main>
        <fieldset>
            <legend>Welcome Librarian <?php echo $name; ?></legend>
            <div class="dashboard-links">
                <a href="librarian/books.php">📚 Book Catalog Management</a><br><br>
                <a href="librarian/pending_requests.php">⏳ Pending Borrow Requests</a><br><br>
                <a href="librarian/returns.php">🔄 Process Returns</a><br><br>
                <a href="librarian/fines_dashboard.php">💰 Fine Management</a><br><br>
                <a href="../Controller/Logout.php">🚪 Logout</a>
            </div>
        </fieldset>
    </main>
    <footer><p>&copy; Library System</p></footer>
</body>
</html>