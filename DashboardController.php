<?php
// controllers/DashboardController.php
require_once 'models/ListModel.php';
require_once 'models/ExpenseModel.php';

class DashboardController {
    
    public function index() {
       
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }
        
        $user_id = $_SESSION['user_id'];
        $listModel = new ListModel();
        $expenseModel = new ExpenseModel();
        
        $pending = $listModel->getPendingItems($user_id);
        $monthlyExpense = $expenseModel->getTotalMonthlyExpense($user_id);
        $expiring = $expenseModel->getExpiringItems($user_id);
        
        include 'views/dashboard.php';
    }
}
?>