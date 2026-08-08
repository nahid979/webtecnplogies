<?php
include "../../Controller/auth_check.php";
auth_check("admin");
include "../../Model/FineModel.php";

$fineModel = new FineModel();
$trends = $fineModel->getBorrowingTrends();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Borrowing Trends Report</title>
    <link rel="stylesheet" href="../../Design/style.css">
</head>
<body>
<header><h1>Borrowing Trends Report (Last 30 Days)</h1></header>
<main>
    <fieldset>
        <legend>Daily Statistics</legend>
        <table border="1" cellpadding="8" style="width:100%">
            <thead>
                <tr style="background:#ddd">
                    <th>Date</th><th>Total Requests</th><th>Active Loans</th><th>Returned</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($trends && $trends->num_rows > 0): ?>
                    <?php while($row = $trends->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['date']; ?></td>
                        <td><?php echo $row['total_requests']; ?></td>
                        <td><?php echo $row['active_loans']; ?></td>
                        <td><?php echo $row['returned']; ?></td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="4">No borrowing data in the last 30 days.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </fieldset>
    <div class="dashboard-links">
        <a href="../AdminDashboard.php">Dashboard</a>
    </div>
</main>
<footer><p>&copy; Library System</p></footer>
</body>
</html>