<?php
// controllers/ListController.php
require_once 'models/ListModel.php';
require_once 'models/ExpenseModel.php';

class ListController {
    private $listModel;
    private $expenseModel;
    
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }
        $this->listModel = new ListModel();
        $this->expenseModel = new ExpenseModel();
    }
    
    public function index() {
        $user_id = $_SESSION['user_id'];
        $pendingItems = $this->listModel->getPendingItems($user_id);
        $boughtItems = $this->listModel->getBoughtItems($user_id);
        include 'views/list.php';
    }
    
    public function addItem() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $user_id = $_SESSION['user_id'];
            $name = $_POST['item_name'];
            $quantity = $_POST['quantity'];
            $category = $_POST['category'];
            
            if ($this->listModel->addItem($user_id, $name, $quantity, $category)) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false]);
            }
        }
    }
    
    public function deleteItem() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $user_id = $_SESSION['user_id'];
            $item_id = $_POST['item_id'];
            
            if ($this->listModel->deleteItem($item_id, $user_id)) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false]);
            }
        }
    }
    
    public function markBought() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $user_id = $_SESSION['user_id'];
            $item_id = $_POST['item_id'];
            $price = $_POST['price'];
            $shop_name = $_POST['shop_name'];
            $expiry_date = $_POST['expiry_date'] ?? null;
            
            if ($this->listModel->markAsBought($item_id, $user_id)) {
                if ($this->expenseModel->addExpense($user_id, $item_id, $price, $shop_name, $expiry_date)) {
                    echo json_encode(['success' => true]);
                } else {
                    echo json_encode(['success' => false, 'msg' => 'খরচ যোগ করতে ব্যর্থ']);
                }
            } else {
                echo json_encode(['success' => false, 'msg' => 'স্ট্যাটাস আপডেট ব্যর্থ']);
            }
        }
    }
    
    public function getPending() {
        $user_id = $_SESSION['user_id'];
        $items = $this->listModel->getPendingItems($user_id);
        header('Content-Type: application/json');
        echo json_encode($items);
    }
    
    
    public function editItem() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $user_id = $_SESSION['user_id'];
            $item_id = $_POST['item_id'];
            $name = $_POST['item_name'];
            $quantity = $_POST['quantity'];
            $category = $_POST['category'];
            
            if ($this->listModel->updateItem($item_id, $user_id, $name, $quantity, $category)) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false]);
            }
        }
    }
    
   
    public function getItem() {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            $user_id = $_SESSION['user_id'];
            $item_id = $_GET['item_id'] ?? 0;
            
            $item = $this->listModel->getItemById($item_id, $user_id);
            if ($item) {
                echo json_encode(['success' => true, 'item' => $item]);
            } else {
                echo json_encode(['success' => false]);
            }
        }
    }
}
?>