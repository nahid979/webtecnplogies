<?php
// models/ExpenseModel.php
require_once 'config/database.php';

class ExpenseModel {
    private $conn;
    
    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }
    
  
    public function addExpense($user_id, $item_id, $price, $shop_name, $expiry_date) {
        $stmt = $this->conn->prepare("INSERT INTO expenses (user_id, item_id, price, shop_name, expiry_date) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iidss", $user_id, $item_id, $price, $shop_name, $expiry_date);
        return $stmt->execute();
    }
    
  
    public function getMonthlyExpenses($user_id) {
        $stmt = $this->conn->prepare("
            SELECT e.*, s.item_name, s.category 
            FROM expenses e 
            JOIN shopping_list s ON e.item_id = s.id 
            WHERE e.user_id = ? 
            AND MONTH(e.bought_at) = MONTH(CURRENT_DATE()) 
            AND YEAR(e.bought_at) = YEAR(CURRENT_DATE())
            ORDER BY e.bought_at DESC
        ");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    

    public function getCategoryWiseExpense($user_id) {
        $stmt = $this->conn->prepare("
            SELECT s.category, SUM(e.price) as total 
            FROM expenses e 
            JOIN shopping_list s ON e.item_id = s.id 
            WHERE e.user_id = ? 
            AND MONTH(e.bought_at) = MONTH(CURRENT_DATE()) 
            AND YEAR(e.bought_at) = YEAR(CURRENT_DATE())
            GROUP BY s.category
        ");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    
  
    public function getTotalMonthlyExpense($user_id) {
        $stmt = $this->conn->prepare("SELECT SUM(price) as total FROM expenses WHERE user_id = ? AND MONTH(bought_at) = MONTH(CURRENT_DATE()) AND YEAR(bought_at) = YEAR(CURRENT_DATE())");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['total'] ?? 0;
    }
    
    
    public function getExpiringItems($user_id) {
        $stmt = $this->conn->prepare("
            SELECT e.*, s.item_name 
            FROM expenses e 
            JOIN shopping_list s ON e.item_id = s.id 
            WHERE e.user_id = ? 
            AND e.expiry_date IS NOT NULL 
            AND e.expiry_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 3 DAY)
            ORDER BY e.expiry_date ASC
        ");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
?>