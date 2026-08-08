<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../Model/FineModel.php';
require_once __DIR__ . '/auth_check.php';

$model = new FineModel();


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    auth_check('librarian');
    $fine_id = (int)($_POST['fine_id'] ?? 0);
    if ($fine_id && $model->markFinePaid($fine_id)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to mark as paid']);
    }
    exit();
}


header("Location: ../View/librarian/fines_dashboard.php");
exit();
?>