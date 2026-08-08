<?php
header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['member_id']) || !in_array($_SESSION['role'], ['librarian', 'admin'])) {
    echo json_encode([]);
    exit();
}

$q = trim($_GET['q'] ?? '');
if ($q === '') {
    echo json_encode([]);
    exit();
}

require_once __DIR__ . '/../Model/BookModel.php';

$model = new BookModel();
$result = $model->searchBooks($q);

$books = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $available = $row['total_copies'] - ($row['borrowed_count'] ?? 0);
        $books[] = [
            'id' => $row['id'],
            'title' => $row['title'],
            'author' => $row['author'],
            'genre_name' => $row['genre_name'] ?? '',
            'total_copies' => (int)$row['total_copies'],
            'available_copies' => (int)$available
        ];
    }
}
echo json_encode($books);
?>