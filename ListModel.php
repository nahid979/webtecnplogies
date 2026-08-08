<?php
// models/ListModel.php
require_once 'config/database.php';

class ListModel {
    private $conn;
    
    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }
    
    public function addItem($user_id, $name, $quantity, $category) {
        $stmt = $this->conn->prepare("INSERT INTO shopping_list (user_id, item_name, quantity, category) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $user_id, $name, $quantity, $category);
        return $stmt->execute();
    }
    
    
    public function getPendingItems($user_id) {
        $stmt = $this->conn->prepare("SELECT * FROM shopping_list WHERE user_id = ? AND status = 'pending' ORDER BY created_at DESC");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    
 
    public function getBoughtItems($user_id) {
        $stmt = $this->conn->prepare("SELECT * FROM shopping_list WHERE user_id = ? AND status = 'bought' ORDER BY created_at DESC LIMIT 10");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    
    public function deleteItem($item_id, $user_id) {
        $stmt = $this->conn->prepare("DELETE FROM shopping_list WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $item_id, $user_id);
        return $stmt->execute();
    }
    
    
    public function markAsBought($item_id, $user_id) {
        $stmt = $this->conn->prepare("UPDATE shopping_list SET status = 'bought' WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $item_id, $user_id);
        return $stmt->execute();
    }
    
    
    public function getItemById($item_id, $user_id) {
        $stmt = $this->conn->prepare("SELECT * FROM shopping_list WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $item_id, $user_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
  
    public function updateItem($item_id, $user_id, $name, $quantity, $category) {
        $stmt = $this->conn->prepare("UPDATE shopping_list SET item_name = ?, quantity = ?, category = ? WHERE id = ? AND user_id = ?");
        $stmt->bind_param("sssii", $name, $quantity, $category, $item_id, $user_id);
        return $stmt->execute();
    }
}
?>