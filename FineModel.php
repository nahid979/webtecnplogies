<?php
require_once __DIR__ . '/db.php';

class FineModel {
    private $conn;

    public function __construct() {
        $db = new db();
        $this->conn = $db->connection();
    }

  
    public function getOverdueActiveBorrows() {
        $sql = "SELECT id, member_id, due_date FROM borrow_records WHERE status = 'Active' AND due_date < CURDATE()";
        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }


    public function insertOrUpdateFine($borrow_record_id, $member_id, $amount) {
        $sql = "INSERT INTO fines (borrow_record_id, member_id, amount, is_paid) VALUES (?, ?, ?, 0)
                ON DUPLICATE KEY UPDATE amount = VALUES(amount), is_paid = 0";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iid", $borrow_record_id, $member_id, $amount);
        return $stmt->execute();
    }


    public function generateFines() {
        $overdue_records = $this->getOverdueActiveBorrows();
        foreach ($overdue_records as $record) {
            $borrow_id = $record['id'];
            $member_id = $record['member_id'];
            $due_date = $record['due_date'];
            $overdue_days = (new DateTime())->diff(new DateTime($due_date))->days;
            $amount = $overdue_days * 5;
            $this->insertOrUpdateFine($borrow_id, $member_id, $amount);
        }
    }

  
    public function getMemberFines($member_id) {
        $sql = "SELECT f.id, f.amount, f.is_paid, f.created_at,
                       b.title, br.due_date, br.return_date,
                       DATEDIFF(NOW(), br.due_date) as days_overdue
                FROM fines f
                JOIN borrow_records br ON f.borrow_record_id = br.id
                JOIN books b ON br.book_id = b.id
                WHERE f.member_id = ? AND f.is_paid = 0
                ORDER BY br.due_date ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $member_id);
        $stmt->execute();
        return $stmt->get_result();
    }

   
    public function getTotalOutstandingFine($member_id) {
        $sql = "SELECT SUM(amount) as total FROM fines WHERE member_id = ? AND is_paid = 0";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $member_id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return $row['total'] ?? 0;
    }

 
    public function getAllUnpaidFines($search = '') {
        $sql = "SELECT f.id, f.amount, f.created_at, m.name as member_name, m.id as member_id,
                       b.title as book_title, br.due_date
                FROM fines f
                JOIN members m ON f.member_id = m.id
                JOIN borrow_records br ON f.borrow_record_id = br.id
                JOIN books b ON br.book_id = b.id
                WHERE f.is_paid = 0";
        if ($search !== '') {
            $sql .= " AND m.name LIKE ?";
        }
        $sql .= " ORDER BY m.name, br.due_date";
        
        $stmt = $this->conn->prepare($sql);
        if ($search !== '') {
            $like = "%$search%";
            $stmt->bind_param("s", $like);
        }
        $stmt->execute();
        return $stmt->get_result();
    }


    public function markFinePaid($fine_id) {
        $stmt = $this->conn->prepare("UPDATE fines SET is_paid = 1 WHERE id = ?");
        $stmt->bind_param("i", $fine_id);
        return $stmt->execute();
    }


    public function getBorrowingTrends() {
        $sql = "SELECT DATE(borrow_date) as date,
                       COUNT(*) as total_requests,
                       SUM(CASE WHEN status = 'Active' THEN 1 ELSE 0 END) as active_loans,
                       SUM(CASE WHEN status = 'Returned' THEN 1 ELSE 0 END) as returned
                FROM borrow_records
                WHERE borrow_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                GROUP BY DATE(borrow_date)
                ORDER BY date DESC";
        return $this->conn->query($sql);
    }

  
    public function getFineById($fine_id) {
        $stmt = $this->conn->prepare("SELECT * FROM fines WHERE id = ?");
        $stmt->bind_param("i", $fine_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
}
?>