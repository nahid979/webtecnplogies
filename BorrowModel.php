<?php
require_once __DIR__ . '/db.php';

class BorrowModel {
    private $conn;

    public function __construct() {
        $db = new db();
        $this->conn = $db->connection();
    }

    public function getAllBooksForMember() {
        $sql = "SELECT b.*, g.name as genre_name,
                (SELECT COUNT(*) FROM borrow_records WHERE book_id = b.id AND status = 'Active') as borrowed_count
                FROM books b
                LEFT JOIN genres g ON b.genre_id = g.id
                ORDER BY b.title ASC";
        return $this->conn->query($sql);
    }

 
    public function createBorrowRequest($member_id, $book_id) {
  
        $check = $this->conn->prepare("SELECT total_copies - (SELECT COUNT(*) FROM borrow_records WHERE book_id = ? AND status = 'Active') as avail FROM books WHERE id = ?");
        $check->bind_param("ii", $book_id, $book_id);
        $check->execute();
        $res = $check->get_result();
        $row = $res->fetch_assoc();
        if ($row['avail'] <= 0) {
            return false;
        }

        $borrow_date = date('Y-m-d');
        $due_date = date('Y-m-d', strtotime('+14 days'));
        $stmt = $this->conn->prepare("INSERT INTO borrow_records (member_id, book_id, status, borrow_date, due_date) VALUES (?, ?, 'Pending', ?, ?)");
        $stmt->bind_param("iiss", $member_id, $book_id, $borrow_date, $due_date);
        return $stmt->execute();
    }

 
    public function getPendingRequests() {
        $sql = "SELECT br.id, br.borrow_date, br.due_date, m.name as member_name, b.title as book_title
                FROM borrow_records br
                JOIN members m ON br.member_id = m.id
                JOIN books b ON br.book_id = b.id
                WHERE br.status = 'Pending'
                ORDER BY br.borrow_date ASC";
        return $this->conn->query($sql);
    }

  
    public function approveRequest($borrow_id) {
        $stmt = $this->conn->prepare("UPDATE borrow_records SET status = 'Active' WHERE id = ? AND status = 'Pending'");
        $stmt->bind_param("i", $borrow_id);
        return $stmt->execute();
    }

  
    public function rejectRequest($borrow_id) {
        $stmt = $this->conn->prepare("DELETE FROM borrow_records WHERE id = ? AND status = 'Pending'");
        $stmt->bind_param("i", $borrow_id);
        return $stmt->execute();
    }

  
    public function searchActiveLoans($keyword) {
        $like = "%{$keyword}%";
        $sql = "SELECT br.id, br.borrow_date, br.due_date, m.name as member_name, b.title as book_title, b.id as book_id
                FROM borrow_records br
                JOIN members m ON br.member_id = m.id
                JOIN books b ON br.book_id = b.id
                WHERE br.status = 'Active' AND (m.name LIKE ? OR b.title LIKE ?)
                ORDER BY br.due_date ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $like, $like);
        $stmt->execute();
        return $stmt->get_result();
    }


    public function processReturn($borrow_id) {
        $this->conn->begin_transaction();
        try {
            // update borrow record
            $stmt = $this->conn->prepare("UPDATE borrow_records SET status = 'Returned', return_date = NOW() WHERE id = ? AND status = 'Active'");
            $stmt->bind_param("i", $borrow_id);
            if (!$stmt->execute()) throw new Exception("Update failed");

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            return false;
        }
    }


    public function getBookAvailability($book_id) {
        $sql = "SELECT total_copies - (SELECT COUNT(*) FROM borrow_records WHERE book_id = ? AND status = 'Active') as available FROM books WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $book_id, $book_id);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();
        return $row['available'] ?? 0;
    }


    public function getBookById($book_id) {
        $stmt = $this->conn->prepare("SELECT id, title FROM books WHERE id = ?");
        $stmt->bind_param("i", $book_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
}
?>