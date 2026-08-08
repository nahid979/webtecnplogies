<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../Model/BookModel.php';
require_once __DIR__ . '/../../Controller/auth_check.php';
auth_check('librarian');

$model = new BookModel();
$genres = $model->getAllGenres();

$msg = $_SESSION['msg'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['msg'], $_SESSION['error']);

$edit_id = isset($_GET['edit']) ? (int)$_GET['edit'] : 0;
$edit_name = '';
if ($edit_id) {
    $res = $model->getGenreById($edit_id);
    if ($row = $res->fetch_assoc()) {
        $edit_name = $row['name'];
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Genres</title>
    <link rel="stylesheet" href="../../Design/style.css">
</head>
<body>
<header><h1>Library Management - Genres</h1></header>
<main>
    <fieldset>
        <legend><?php echo $edit_id ? 'Edit Genre' : 'Add New Genre'; ?></legend>
        <?php if ($error): ?>
            <div class="error-msg"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($msg): ?>
            <div style="color:green"><?php echo htmlspecialchars($msg); ?></div>
        <?php endif; ?>
        <form method="post" action="../../Controller/GenreController.php">
            <?php if ($edit_id): ?>
                <input type="hidden" name="id" value="<?php echo $edit_id; ?>">
                <input type="hidden" name="edit_genre" value="1">
            <?php else: ?>
                <input type="hidden" name="add_genre" value="1">
            <?php endif; ?>
            <div class="form-group">
                <label>Genre Name</label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($edit_name); ?>" required>
            </div>
            <input type="submit" class="submit-btn" value="<?php echo $edit_id ? 'Update' : 'Add'; ?>">
        </form>
    </fieldset>

    <fieldset style="margin-top:20px">
        <legend>All Genres</legend>
        <table border="1" cellpadding="8" style="width:100%; border-collapse:collapse;">
            <tr style="background:#ddd">
                <th>ID</th><th>Genre Name</th><th>Action</th>
            </tr>
            <?php while($row = $genres->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo htmlspecialchars($row['name']); ?></td>
                <td>
                    <a href="genres.php?edit=<?php echo $row['id']; ?>">Edit</a> |
                    <a href="../../Controller/GenreController.php?delete=<?php echo $row['id']; ?>" onclick="return confirm('Delete genre?')">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </fieldset>
    <div class="dashboard-links">
        <a href="books.php">Back to Books</a> |
        <a href="../LibrarianDashboard.php">Dashboard</a>
    </div>
</main>
<footer><p>&copy; Library System</p></footer>
</body>
</html>