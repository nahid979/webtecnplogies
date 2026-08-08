<?php
include "../../Controller/auth_check.php";
auth_check("member");
include "../../Model/FineModel.php";

$fineModel = new FineModel();
$fineModel->generateFines();

$member_id = $_SESSION['member_id'];
$fines = $fineModel->getMemberFines($member_id);
$total = $fineModel->getTotalOutstandingFine($member_id);

$msg = $_SESSION['msg'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['msg'], $_SESSION['error']);
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Fines</title>
    <link rel="stylesheet" href="../../Design/style.css">
</head>
<body>
<header><h1>My Fines</h1></header>
<main>
    <fieldset>
        <legend>Outstanding Fines</legend>
        <?php if ($msg): ?>
            <div style="color:green"><?php echo htmlspecialchars($msg); ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="error-msg"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <p style="font-size:18px; font-weight:bold;">Total Outstanding: <?php echo number_format($total, 2); ?> units</p>
        <table border="1" cellpadding="8" style="width:100%; border-collapse:collapse; margin-top:15px;">
            <thead>
                <tr style="background:#ddd">
                    <th>Book Title</th>
                    <th>Due Date</th>
                    <th>Return Date</th>
                    <th>Days Overdue</th>
                    <th>Fine Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($fines->num_rows > 0): ?>
                    <?php while($row = $fines->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['title']); ?></td>
                        <td><?php echo $row['due_date']; ?></td>
                        <td><?php echo $row['return_date'] ?? 'Not Yet Returned'; ?></td>
                        <td><?php echo $row['days_overdue']; ?> days</td>
                        <td><?php echo number_format($row['amount'], 2); ?></td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="5" style="text-align:center;">No unpaid fines. Great job!</td></tr>
                <?php endif; ?>
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