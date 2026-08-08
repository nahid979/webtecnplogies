<?php
// Model/BookModel.php
require_once __DIR__ . '/db.php';

class BookModel {
    private $conn;

    public function __construct() {
        $db = new db();
        $this->conn = $db->connection();
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    // ============ GENRE CRUD ============
    public function getAllGenres() {
        $sql = "SELECT * FROM genres ORDER BY name";
        $result = $this->conn->query($sql);
        return $result;
    }

    public function getGenreById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM genres WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function addGenre($name) {
        $stmt = $this->conn->prepare("INSERT INTO genres (name) VALUES (?)");
        $stmt->bind_param("s", $name);
        return $stmt->execute();
    }

    public function updateGenre($id, $name) {
        $stmt = $this->conn->prepare("UPDATE genres SET name = ? WHERE id = ?");
        $stmt->bind_param("si", $name, $id);
        return $stmt->execute();
    }

    public function deleteGenre($id) {
        $check = $this->conn->prepare("SELECT COUNT(*) as cnt FROM books WHERE genre_id = ?");
        $check->bind_param("i", $id);
        $check->execute();
        $res = $check->get_result();
        $row = $res->fetch_assoc();
        if ($row['cnt'] > 0) {
            return false;
        }
        $del = $this->conn->prepare("DELETE FROM genres WHERE id = ?");
        $del->bind_param("i", $id);
        return $del->execute();
    }

    // ============ BOOK CRUD ============
    public function getAllBooks() {
        $sql = "SELECT b.*, g.name as genre_name,
                (SELECT COUNT(*) FROM borrow_records WHERE book_id = b.id AND status = 'Active') as borrowed_count
                FROM books b
                LEFT JOIN genres g ON b.genre_id = g.id
                ORDER BY b.id DESC";
        return $this->conn->query($sql);
    }

    public function getBookById($id) {
        $stmt = $this->conn->prepare("SELECT b.*, g.name as genre_name FROM books b LEFT JOIN genres g ON b.genre_id = g.id WHERE b.id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function addBook($title, $author, $isbn, $genre_id, $total_copies, $shelf_location, $published_year) {
        $stmt = $this->conn->prepare("INSERT INTO books (title, author, isbn, genre_id, total_copies, shelf_location, published_year) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssiiss", $title, $author, $isbn, $genre_id, $total_copies, $shelf_location, $published_year);
        return $stmt->execute();
    }

    public function updateBook($id, $title, $author, $isbn, $genre_id, $total_copies, $shelf_location, $published_year) {
        $stmt = $this->conn->prepare("UPDATE books SET title=?, author=?, isbn=?, genre_id=?, total_copies=?, shelf_location=?, published_year=? WHERE id=?");
        $stmt->bind_param("sssiissi", $title, $author, $isbn, $genre_id, $total_copies, $shelf_location, $published_year, $id);
        return $stmt->execute();
    }

    public function deleteBook($id) {
        $check = $this->conn->prepare("SELECT COUNT(*) as cnt FROM borrow_records WHERE book_id = ? AND status IN ('Pending','Active')");
        $check->bind_param("i", $id);
        $check->execute();
        $res = $check->get_result();
        $row = $res->fetch_assoc();
        if ($row['cnt'] > 0) {
            return false;
        }
        $del = $this->conn->prepare("DELETE FROM books WHERE id = ?");
        $del->bind_param("i", $id);
        return $del->execute();
    }

    public function searchBooks($keyword) {
    $like = "%" . $keyword . "%";
    $sql = "SELECT b.*, g.name as genre_name,
                (SELECT COUNT(*) FROM borrow_records WHERE book_id = b.id AND status = 'Active') as borrowed_count
            FROM books b
            LEFT JOIN genres g ON b.genre_id = g.id
            WHERE b.title LIKE ? OR b.author LIKE ? OR b.isbn LIKE ?
            ORDER BY b.id DESC";
    
    $stmt = $this->conn->prepare($sql);
    if (!$stmt) {
        return false;
    }
    $stmt->bind_param("sss", $like, $like, $like);
    $stmt->execute();
    return $stmt->get_result();
}
}
?>