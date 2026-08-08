<?php
include "../../Controller/auth_check.php";
auth_check("librarian");
include "../../Model/FineModel.php";

$fineModel = new FineModel();
$search = $_GET['search'] ?? '';
$fines = $fineModel->getAllUnpaidFines($search);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Fine Management</title>  
    
    <link rel="stylesheet" href="../../Design/style.css">
    <script>
        function markAsPaid(fineId, rowId) {
            if (!confirm('Mark this fine as paid?')) return;
            var xhr = new XMLHttpRequest();
            xhr.open('POST', '../../Controller/FineController.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    var res = JSON.parse(xhr.responseText);
                    if (res.success) {
                        var row = document.getElementById('row_' + rowId);
                        if (row) row.remove();
                        alert('Fine marked as paid.');
                    } else {
                        alert('Error: ' + res.message);
                    }
                }
            };
            xhr.send('fine_id=' + fineId);
        }
    </script>
</head>
<body>
<header><h1>Fine Management</h1></header>
<main>
    <fieldset>
        <legend>Search Unpaid Fines</legend>
        <form method="get" action="">
            <div class="form-group" style="display:flex; gap:10px;">
                <input type="text" name="search" placeholder="Member name" value="<?php echo htmlspecialchars($search); ?>" style="flex:1;">
                <button type="submit" class="submit-btn" style="width:auto;">Search</button>
            </div>
        </form>
    </fieldset>

    <fieldset style="margin-top:20px">
        <legend>All Unpaid Fines</legend>
        <table border="1" cellpadding="8" style="width:100%">
            <thead>
                <tr style="background:#ddd">
                    <th>Member Name</th><th>Book Title</th><th>Due Date</th><th>Fine Amount</th><th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($fines->num_rows > 0): ?>
                    <?php $count = 0; ?>
                    <?php while($row = $fines->fetch_assoc()): ?>
                    <tr id="row_<?php echo $count; ?>">
                        <td><?php echo htmlspecialchars($row['member_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['book_title']); ?></td>
                        <td><?php echo $row['due_date']; ?></td>
                        <td><?php echo number_format($row['amount'], 2); ?></td>
                        <td><button onclick="markAsPaid(<?php echo $row['id']; ?>, <?php echo $count; ?>)">Mark as Paid</button></td>
                    </tr>
                    <?php $count++; endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="5">No unpaid fines found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </fieldset>
    <div class="dashboard-links">
        <a href="../LibrarianDashboard.php">Dashboard</a>
    </div>
</main>
</body>
</html>