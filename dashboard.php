<?php
// views/dashboard.php
// session_start() সরানো হয়েছে (index.php ইতিমধ্যেই সেশন চালু করে)

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?action=login');
    exit;
}

require_once 'models/ListModel.php';
require_once 'models/ExpenseModel.php';

$user_id = $_SESSION['user_id'];
$listModel = new ListModel();
$expenseModel = new ExpenseModel();

$pending = $listModel->getPendingItems($user_id);
$monthlyExpense = $expenseModel->getTotalMonthlyExpense($user_id);
$expiring = $expenseModel->getExpiringItems($user_id);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>ড্যাশবোর্ড - বাজার হিসাব</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div style="background:#0d1520; padding:10px 20px; border-bottom:1px solid #1a2a3a;">
        <div style="color:#00d4ff; font-weight:bold; text-align:center; font-size:20px;">
            🛒 বাজার হিসাব
        </div>
    </div>

    <div class="app-container">
        <div class="sidebar">
            <h2>🛒 বাজার হিসাব</h2>
            <p>👋 স্বাগতম <?php echo $_SESSION['user_name']; ?></p>
            <hr>
            <a href="index.php?action=dashboard">📊 ড্যাশবোর্ড</a>
            <a href="index.php?action=list">📝 বাজার তালিকা</a>
            <a href="index.php?action=expenses">💰 খরচের হিসাব</a>
            <a href="index.php?action=logout">🚪 লগআউট</a>
        </div>

        <div class="main-content">
            <h2>📊 ড্যাশবোর্ড</h2>
            
            <div class="stats">
                <div class="stat-box">
                    <h3>এই মাসের খরচ</h3>
                    <p>৳ <?php echo number_format($monthlyExpense, 2); ?></p>
                </div>
                <div class="stat-box">
                    <h3>কিনতে বাকি</h3>
                    <p><?php echo count($pending); ?> টি</p>
                </div>
                <div class="stat-box danger">
                    <h3>⏳ মেয়াদ শেষের কাছাকাছি</h3>
                    <p><?php echo count($expiring); ?> টি</p>
                </div>
            </div>

            <?php if(count($expiring) > 0): ?>
            <div class="alert alert-danger">
                <strong>⚠️ সতর্কতা! নিচের জিনিসগুলোর মেয়াদ শেষ হতে ৩ দিন বাকি:</strong>
                <ul>
                <?php foreach($expiring as $item): ?>
                    <li><?php echo $item['item_name']; ?> (মেয়াদ: <?php echo date('d M Y', strtotime($item['expiry_date'])); ?>)</li>
                <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>

            <div class="pending-list">
                <h3>📋 কিনতে বাকি</h3>
                <?php if(count($pending) > 0): ?>
                <ul>
                <?php foreach($pending as $item): ?>
                    <li>🛒 <?php echo $item['item_name']; ?> (<?php echo $item['quantity']; ?>) - <?php echo $item['category']; ?></li>
                <?php endforeach; ?>
                </ul>
                <?php else: ?>
                <p>🎉 কোনো পেন্ডিং আইটেম নেই!</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="assets/js/script.js"></script>
    <script src="assets/js/voice.js"></script>
</body>
</html>