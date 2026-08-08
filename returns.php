<?php
include "../../Controller/auth_check.php";
auth_check("librarian");
include "../../Model/BorrowModel.php";

$model = new BorrowModel();
$search = $_GET['search'] ?? '';
$results = [];
if ($search !== '') {
    $results = $model->searchActiveLoans($search);
}
$msg = $_SESSION['msg'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['msg'], $_SESSION['error']);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Process Returns</title>
    <link rel="stylesheet" href="../../Design/style.css">
</head>
<body>
<header><h1>Process Returns</h1></header>
<main>
    <fieldset>
        <legend>Search Active Loans</legend>
        <form method="get" action="">
            <div class="form-group" style="display:flex; gap:10px;">
                <input type="text" name="search" placeholder="Member name or book title" value="<?php echo htmlspecialchars($search); ?>" style="flex:1;">
                <button type="submit" class="submit-btn" style="width:auto;">Search</button>
            </div>
        </form>
    </fieldset>

    <?php if ($search !== ''): ?>
    <fieldset style="margin-top:20px">
        <legend>Active Loans</legend>
        <?php if ($msg) echo "<div style='color:green'>$msg</div>"; ?>
        <?php if ($error) echo "<div class='error-msg'>$error</div>"; ?>
        <table border="1" cellpadding="8" style="width:100%">
            <thead>
                <tr style="background:#ddd">
                    <th>Member</th><th>Book</th><th>Borrow Date</th><th>Due Date</th><th>Action</th>
                <tr>
            </thead>
            <tbody>
                <?php if ($results && $results->num_rows > 0): ?>
                    <?php while($row = $results->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['member_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['book_title']); ?></a>
                        <td><?php echo $row['borrow_date']; ?></td>
                        <td><?php echo $row['due_date']; ?></a>
                        <td>
                            <form method="post" action="../../Controller/ReturnController.php" onsubmit="return confirm('Process return?')">
                                <input type="hidden" name="borrow_id" value="<?php echo $row['id']; ?>">
                                <input type="hidden" name="process_return" value="1">
                                <button type="submit" class="submit-btn" style="padding:5px 10px;">Process Return</button>
                            </form>
                        </a>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="5">No active loans found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </fieldset>
    <?php endif; ?>
    <div class="dashboard-links">
        <a href="../LibrarianDashboard.php">Dashboard</a>
    </div>
</main>
</body>
</html>