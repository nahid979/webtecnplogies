<?php
header('Content-Type: application/json');
if (session_status() === PHP_SESSION_NONE) session_start();


if (!isset($_SESSION['member_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$book_id = (int)($_GET['id'] ?? 0);
if ($book_id <= 0) {
    echo json_encode(['error' => 'Invalid book id']);
    exit();
}

require_once __DIR__ . '/../Model/BorrowModel.php';
$model = new BorrowModel();
$available = $model->getBookAvailability($book_id);
echo json_encode(['available' => $available]);
?>