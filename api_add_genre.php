<?php
header('Content-Type: application/json');
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['member_id']) || !in_array($_SESSION['role'], ['librarian', 'admin'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

require_once __DIR__ . '/../Model/BookModel.php';

$name = trim($_POST['name'] ?? '');
if ($name === '') {
    echo json_encode(['success' => false, 'message' => 'Genre name required']);
    exit();
}

$model = new BookModel();
if ($model->addGenre($name)) {
    $conn = $model->getConnection(); 
    $newId = $conn->insert_id;
    echo json_encode(['success' => true, 'id' => $newId, 'name' => $name]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to add genre']);
}
?>