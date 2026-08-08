<?php
include "../../Controller/auth_check.php";
auth_check("librarian");
include "../../Model/BorrowModel.php";

$model = new BorrowModel();
$pending = $model->getPendingRequests();

$msg = $_SESSION['msg'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['msg'], $_SESSION['error']);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Pending Borrow Requests</title>
    <link rel="stylesheet" href="../../Design/style.css">
    <script>
        function approveRequest(id) {
            if (!confirm('Approve this request?')) return;
            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    var row = document.getElementById('row_' + id);
                    if (row) row.remove();
                    alert('Request approved.');
                }
            };
            xhr.open("GET", "../../Controller/BorrowController.php?approve=" + id, true);
            xhr.send();
        }

        function rejectRequest(id) {
            if (!confirm('Reject this request? This will delete the request.')) return;
            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    var row = document.getElementById('row_' + id);
                    if (row) row.remove();
                    alert('Request rejected.');
                }
            };
            xhr.open("GET", "../../Controller/BorrowController.php?reject=" + id, true);
            xhr.send();
        }
    </script>
</head>
<body>
<header><h1>Pending Borrow Requests</h1></header>
<main>
    <fieldset>
        <legend>Requests Awaiting Approval</legend>
        <?php if ($msg) echo "<div style='color:green'>$msg</div>"; ?>
        <?php if ($error) echo "<div class='error-msg'>$error</div>"; ?>
        <table border="1" cellpadding="8" style="width:100%">
            <thead>
                <tr style="background:#ddd">
                    <th>Member</th><th>Book</th><th>Request Date</th><th>Due Date</th><th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $pending->fetch_assoc()): ?>
                <tr id="row_<?php echo $row['id']; ?>">
                    <td><?php echo htmlspecialchars($row['member_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['book_title']); ?></td>
                    <td><?php echo $row['borrow_date']; ?></td>
                    <td><?php echo $row['due_date']; ?></td>
                    <td>
                        <button onclick="approveRequest(<?php echo $row['id']; ?>)">Approve</button>
                        <button onclick="rejectRequest(<?php echo $row['id']; ?>)">Reject</button>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </fieldset>
    <div class="dashboard-links">
        <a href="../LibrarianDashboard.php">Dashboard</a>
    </div>
</main>
</body>
</html>