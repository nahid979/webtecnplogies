<?php
// controllers/ExpenseController.php
require_once 'models/ExpenseModel.php';

class ExpenseController {
    private $model;
    
    public function __construct() {
        
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }
        $this->model = new ExpenseModel();
    }
    
    public function index() {
        $user_id = $_SESSION['user_id'];
        $expenses = $this->model->getMonthlyExpenses($user_id);
        $categoryData = $this->model->getCategoryWiseExpense($user_id);
        $total = $this->model->getTotalMonthlyExpense($user_id);
        
        include 'views/expenses.php';
    }
}
?>