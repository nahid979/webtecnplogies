<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../Model/BorrowModel.php';
require_once __DIR__ . '/auth_check.php';

$model = new BorrowModel();


if (isset($_GET['approve'])) {
    auth_check('librarian');
    $id = (int)$_GET['approve'];
    if ($model->approveRequest($id)) {
        echo "approved";
    } else {
        echo "error";
    }
    exit();
}


if (isset($_GET['reject'])) {
    auth_check('librarian');
    $id = (int)$_GET['reject'];
    if ($model->rejectRequest($id)) {
        echo "rejected";
    } else {
        echo "error";
    }
    exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['borrow_book'])) {
    auth_check('member');
    $member_id = $_SESSION['member_id'];
    $book_id = (int)$_POST['book_id'];
    if ($model->createBorrowRequest($member_id, $book_id)) {
        $_SESSION['msg'] = "Borrow request submitted. Waiting for librarian approval.";
    } else {
        $_SESSION['error'] = "Cannot borrow. No copies available.";
    }
    header("Location: ../View/member/books.php");
    exit();
}

header("Location: ../View/member/books.php");
exit();
?>