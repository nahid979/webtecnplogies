<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../Model/BorrowModel.php';
require_once __DIR__ . '/auth_check.php';
auth_check('librarian');

$model = new BorrowModel();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['process_return'])) {
    $borrow_id = (int)$_POST['borrow_id'];
    if ($model->processReturn($borrow_id)) {
        $_SESSION['msg'] = "Return processed successfully.";
    } else {
        $_SESSION['error'] = "Failed to process return.";
    }
    header("Location: ../View/librarian/returns.php?search=" . urlencode($_POST['search'] ?? ''));
    exit();
}

header("Location: ../View/librarian/returns.php");
exit();
?>