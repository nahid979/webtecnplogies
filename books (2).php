<?php
include "../../Controller/auth_check.php";
auth_check("member");
include "../../Model/BorrowModel.php";

$model = new BorrowModel();
$books = $model->getAllBooksForMember();

$msg = $_SESSION['msg'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['msg'], $_SESSION['error']);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Browse Books</title>
    <link rel="stylesheet" href="../../Design/style.css">
</head>
<body>
<header><h1>Library Management - Browse Books</h1></header>
<main>
    <fieldset>
        <legend>Available Books</legend>
        <?php if ($msg): ?>
            <div style="color:green"><?php echo htmlspecialchars($msg); ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="error-msg"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <table border="1" cellpadding="8" style="width:100%; border-collapse:collapse;">
            <thead>
                <tr style="background:#ddd">
                    <th>Title</th>
                    <th>Author</th>
                    <th>Genre</th>
                    <th>Available Copies</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $books->fetch_assoc()):
                    $available = $row['total_copies'] - ($row['borrowed_count'] ?? 0);
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['title']); ?></td>
                    <td><?php echo htmlspecialchars($row['author']); ?></td>
                    <td><?php echo htmlspecialchars($row['genre_name']); ?></td>
                    <td><?php echo $available; ?></td>
                    <td>

                        <?php if ($available > 0): ?>
                            <form method="post" action="../../Controller/BorrowController.php" onsubmit="return confirm('Request to borrow this book?')" style="display:inline;">
                                <input type="hidden" name="book_id" value="<?php echo $row['id']; ?>">
                                <input type="hidden" name="borrow_book" value="1">
                                <button type="submit" class="submit-btn" style="padding:5px 10px;">Borrow</button>
                            </form>
                        <?php else: ?>
                            <span style="color:red;">Not Available</span>
                        <?php endif; ?>

                        <a href="book_detail.php?id=<?php echo $row['id']; ?>" style="margin-left:10px;">Details</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </fieldset>
    <div class="dashboard-links">
        <a href="../MemberDashboard.php">Back to Dashboard</a>
    </div>
</main>
<footer><p>&copy; Library System</p></footer>
</body>
</html>