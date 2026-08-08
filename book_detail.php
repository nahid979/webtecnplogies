<?php
include "../../Controller/auth_check.php";
auth_check("member");
include "../../Model/BorrowModel.php";

$book_id = (int)($_GET['id'] ?? 0);
if ($book_id <= 0) {
    header("Location: books.php");
    exit();
}

$model = new BorrowModel();
$book = $model->getBookById($book_id);
if (!$book) {
    header("Location: books.php");
    exit();
}
$available = $model->getBookAvailability($book_id);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Book Details</title>
    <link rel="stylesheet" href="../../Design/style.css">
    <script>
        function refreshAvailability() {
            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    var data = JSON.parse(xhr.responseText);
                    var badge = document.getElementById('availability_badge');
                    if (data.available > 0) {
                        badge.innerHTML = 'Available (' + data.available + ' copies)';
                        badge.style.color = 'green';
                    } else {
                        badge.innerHTML = 'Not Available';
                        badge.style.color = 'red';
                    }
                }
            };
            xhr.open("GET", "../../Controller/api_book_availability.php?id=<?php echo $book_id; ?>", true);
            xhr.send();
        }

        setInterval(refreshAvailability, 30000);
    </script>
</head>
<body>
<header><h1>Book Details</h1></header>
<main>
    <fieldset>
        <legend><?php echo htmlspecialchars($book['title']); ?></legend>
        <p><strong>Availability:</strong> <span id="availability_badge" style="font-weight:bold;"><?php echo ($available > 0) ? 'Available' : 'Not Available'; ?></span></p>
        <p><strong>ID:</strong> <?php echo $book['id']; ?></p>
        <div class="dashboard-links">
            <a href="books.php">Back to Book List</a>
        </div>
    </fieldset>
</main>
</body>
</html>