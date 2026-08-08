<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../Model/BookModel.php';
require_once __DIR__ . '/auth_check.php';
auth_check('librarian');

$model = new BookModel();

// ADD Genre
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_genre'])) {
    $name = trim($_POST['name'] ?? '');
    if ($name !== '') {
        if ($model->addGenre($name)) {
            $_SESSION['msg'] = "Genre added successfully";
        } else {
            $_SESSION['error'] = "Failed to add genre";
        }
    } else {
        $_SESSION['error'] = "Genre name required";
    }
    header("Location: ../View/librarian/genres.php");
    exit();
}

// EDIT Genre
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_genre'])) {
    $id = (int)($_POST['id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    if ($id > 0 && $name !== '') {
        if ($model->updateGenre($id, $name)) {
            $_SESSION['msg'] = "Genre updated";
        } else {
            $_SESSION['error'] = "Update failed";
        }
    } else {
        $_SESSION['error'] = "Invalid data";
    }
    header("Location: ../View/librarian/genres.php");
    exit();
}

// DELETE Genre
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($model->deleteGenre($id)) {
        $_SESSION['msg'] = "Genre deleted";
    } else {
        $_SESSION['error'] = "Cannot delete genre. Books exist in this genre.";
    }
    header("Location: ../View/librarian/genres.php");
    exit();
}

header("Location: ../View/librarian/genres.php");
exit();
?>