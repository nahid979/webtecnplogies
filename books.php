<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../Model/BookModel.php';
require_once __DIR__ . '/../../Controller/auth_check.php';
auth_check('librarian');

$model = new BookModel();
$genres = $model->getAllGenres();
$books = $model->getAllBooks();

$msg = $_SESSION['msg'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['msg'], $_SESSION['error']);

$edit_id = isset($_GET['edit']) ? (int)$_GET['edit'] : 0;
$edit_book = null;
if ($edit_id) {
    $res = $model->getBookById($edit_id);
    $edit_book = $res->fetch_assoc();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Book Catalog</title>
    <link rel="stylesheet" href="../../Design/style.css">
    <script src="/-Web_Project_Spring_25_26_G2/Controller/JS/book_search.js"></script>
</head>
<body>
<header><h1>Library Management - Books</h1></header>
<main>
    <!-- ========== ADD/EDIT BOOK FORM ========== -->
    <fieldset>
        <legend><?php echo $edit_book ? 'Edit Book' : 'Add New Book'; ?></legend>
        <?php if ($error): ?>
            <div class="error-msg"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($msg): ?>
            <div style="color:green"><?php echo htmlspecialchars($msg); ?></div>
        <?php endif; ?>
        <form method="post" action="../../Controller/BookController.php">
            <?php if ($edit_book): ?>
                <input type="hidden" name="id" value="<?php echo $edit_book['id']; ?>">
                <input type="hidden" name="edit_book" value="1">
            <?php else: ?>
                <input type="hidden" name="add_book" value="1">
            <?php endif; ?>
            <div class="form-group">
                <label>Title</label>
                <input type="text" name="title" value="<?php echo htmlspecialchars($edit_book['title'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label>Author</label>
                <input type="text" name="author" value="<?php echo htmlspecialchars($edit_book['author'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label>ISBN (10 or 13 digits)</label>
                <input type="text" name="isbn" value="<?php echo htmlspecialchars($edit_book['isbn'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label>Genre</label>
                <select name="genre_id" required>
                    <option value="">-- Select Genre --</option>
                    <?php 
                    $genres->data_seek(0);
                    while($g = $genres->fetch_assoc()): 
                    ?>
                        <option value="<?php echo $g['id']; ?>" <?php echo ($edit_book && $edit_book['genre_id'] == $g['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($g['name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                <?php if ($genres->num_rows == 0): ?>
                    <div style="margin-top:5px;">
                        <a href="genres.php" style="color:red;">+ Add Genre First</a>
                    </div>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label>Total Copies (positive integer)</label>
                <input type="number" name="total_copies" value="<?php echo htmlspecialchars($edit_book['total_copies'] ?? 1); ?>" min="1" required>
            </div>
            <div class="form-group">
                <label>Shelf Location</label>
                <input type="text" name="shelf_location" value="<?php echo htmlspecialchars($edit_book['shelf_location'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label>Publication Year</label>
                <input type="number" name="published_year" value="<?php echo htmlspecialchars($edit_book['published_year'] ?? ''); ?>">
            </div>
            <input type="submit" class="submit-btn" value="<?php echo $edit_book ? 'Update Book' : 'Add Book'; ?>">
        </form>
    </fieldset>

    <!-- ========== QUICK ADD GENRE (আপনার চাওয়া অংশ) ========== -->
    <fieldset style="margin-top: 15px;">
        <legend>Quick Add Genre</legend>
        <form method="post" action="../../Controller/GenreController.php" style="display: flex; gap: 10px; align-items: center;">
            <input type="hidden" name="add_genre" value="1">
            <div class="form-group" style="flex:1; margin-bottom:0;">
                <input type="text" name="name" placeholder="Enter new genre name" required style="width:100%;">
            </div>
            <button type="submit" class="submit-btn" style="width: auto; margin-top:0;">Add Genre</button>
        </form>
    </fieldset>

    <!-- ========== BOOK LIST + LIVE SEARCH ========== -->
    <fieldset style="margin-top:20px">
        <legend>Book List</legend>
        <div class="form-group">
            <label>Live Search: </label>
            <input type="text" id="search_input" onkeyup="searchBooks()" placeholder="Search by title, author or ISBN">
        </div>
        <table border="1" cellpadding="8" style="width:100%; border-collapse:collapse;">
            <thead>
                <tr style="background:#ddd">
                    <th>Title</th><th>Author</th><th>Genre</th><th>Total Copies</th><th>Available Copies</th><th>Action</th>
                 </tr>
            </thead>
            <tbody id="books_table_body">
                <?php 
                $books->data_seek(0);
                while($row = $books->fetch_assoc()):
                    $available = $row['total_copies'] - ($row['borrowed_count'] ?? 0);
                    $rowClass = ($available == 0) ? 'style="background:#ffcccc"' : '';
                ?>
                <tr <?php echo $rowClass; ?>>
                    <td><?php echo htmlspecialchars($row['title']); ?></td>
                    <td><?php echo htmlspecialchars($row['author']); ?></td>
                    <td><?php echo htmlspecialchars($row['genre_name']); ?></td>
                    <td><?php echo $row['total_copies']; ?></td>
                    <td><?php echo $available; ?></td>
                    <td>
                        <a href="books.php?edit=<?php echo $row['id']; ?>">Edit</a> |
                        <a href="../../Controller/BookController.php?delete=<?php echo $row['id']; ?>" onclick="return confirm('Delete book?')">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </fieldset>
    <div class="dashboard-links">
        <a href="genres.php">Manage Genres</a> |
        <a href="../LibrarianDashboard.php">Dashboard</a>
    </div>
</main>
<footer><p>&copy; Library System</p></footer>
</body>
</html>