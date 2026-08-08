<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../Model/BookModel.php';
require_once __DIR__ . '/auth_check.php';
auth_check('librarian');

$model = new BookModel();

// ADD Book
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_book'])) {
    $title = trim($_POST['title'] ?? '');
    $author = trim($_POST['author'] ?? '');
    $isbn = trim($_POST['isbn'] ?? '');
    $genre_id = (int)($_POST['genre_id'] ?? 0);
    $total_copies = (int)($_POST['total_copies'] ?? 0);
    $shelf_location = trim($_POST['shelf_location'] ?? '');
    $published_year = (int)($_POST['published_year'] ?? 0);

    if (!preg_match('/^\d{10}$|^\d{13}$/', $isbn)) {
        $_SESSION['error'] = "Invalid ISBN (must be 10 or 13 digits)";
        header("Location: ../View/librarian/books.php");
        exit();
    }
    if ($total_copies < 1) {
        $_SESSION['error'] = "Total copies must be positive integer";
        header("Location: ../View/librarian/books.php");
        exit();
    }
    if ($model->addBook($title, $author, $isbn, $genre_id, $total_copies, $shelf_location, $published_year)) {
        $_SESSION['msg'] = "Book added successfully";
    } else {
        $_SESSION['error'] = "Failed to add book";
    }
    header("Location: ../View/librarian/books.php");
    exit();
}

// EDIT Book
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_book'])) {
    $id = (int)($_POST['id'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    $author = trim($_POST['author'] ?? '');
    $isbn = trim($_POST['isbn'] ?? '');
    $genre_id = (int)($_POST['genre_id'] ?? 0);
    $total_copies = (int)($_POST['total_copies'] ?? 0);
    $shelf_location = trim($_POST['shelf_location'] ?? '');
    $published_year = (int)($_POST['published_year'] ?? 0);

    if (!preg_match('/^\d{10}$|^\d{13}$/', $isbn)) {
        $_SESSION['error'] = "Invalid ISBN";
        header("Location: ../View/librarian/books.php");
        exit();
    }
    if ($total_copies < 1) {
        $_SESSION['error'] = "Total copies must be positive";
        header("Location: ../View/librarian/books.php");
        exit();
    }
    if ($model->updateBook($id, $title, $author, $isbn, $genre_id, $total_copies, $shelf_location, $published_year)) {
        $_SESSION['msg'] = "Book updated";
    } else {
        $_SESSION['error'] = "Update failed";
    }
    header("Location: ../View/librarian/books.php");
    exit();
}

// DELETE Book
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($model->deleteBook($id)) {
        $_SESSION['msg'] = "Book deleted";
    } else {
        $_SESSION['error'] = "Cannot delete book. There are active borrow records.";
    }
    header("Location: ../View/librarian/books.php");
    exit();
}

header("Location: ../View/librarian/books.php");
exit();
?>