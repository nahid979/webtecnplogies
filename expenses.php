<?php
// views/expenses.php
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?action=login');
    exit;
}

require_once 'models/ExpenseModel.php';

$user_id = $_SESSION['user_id'];
$expenseModel = new ExpenseModel();
$expenses = $expenseModel->getMonthlyExpenses($user_id);
$categoryData = $expenseModel->getCategoryWiseExpense($user_id);
$total = $expenseModel->getTotalMonthlyExpense($user_id);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>খরচের হিসাব - বাজার হিসাব</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        /* প্রিন্ট স্টাইল - শুধু টেবিল প্রিন্ট হবে */
        @media print {
            body * {
                visibility: hidden;
            }
            #printArea, #printArea * {
                visibility: visible;
            }
            #printArea {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                padding: 20px;
                background: #fff;
                color: #000;
            }
            #printArea h2, #printArea h3, #printArea table {
                color: #000 !important;
            }
            #printArea table th {
                background: #f0f0f0 !important;
                color: #000 !important;
            }
            #printArea table td {
                border-bottom: 1px solid #ddd !important;
                color: #000 !important;
            }
            .no-print {
                display: none !important;
            }
            .print-btn {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <!-- হেডার -->
    <div style="background:#0d1520; padding:10px 20px; border-bottom:1px solid #1a2a3a;">
        <div style="color:#00d4ff; font-weight:bold; text-align:center; font-size:20px;">
            🛒 বাজার হিসাব
        </div>
    </div>

    <div class="app-container">
        <div class="sidebar no-print">
            <h2>🛒 বাজার হিসাব</h2>
            <p>👋 স্বাগতম <?php echo $_SESSION['user_name']; ?></p>
            <hr>
            <a href="index.php?action=dashboard">📊 ড্যাশবোর্ড</a>
            <a href="index.php?action=list">📝 বাজার তালিকা</a>
            <a href="index.php?action=expenses">💰 খরচের হিসাব</a>
            <a href="index.php?action=logout">🚪 লগআউট</a>
        </div>

        <div class="main-content">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap;">
                <h2>💰 এই মাসের খরচের হিসাব</h2>
                <!-- প্রিন্ট বাটন -->
                <button onclick="window.print()" class="btn btn-primary print-btn no-print" style="padding: 10px 20px;">
                    🖨️ প্রিন্ট / PDF ডাউনলোড
                </button>
            </div>

            <!-- যেটা প্রিন্ট হবে -->
            <div id="printArea">
                <h3 style="color:#00d4ff;">মোট খরচ: ৳ <?php echo number_format($total, 2); ?></h3>

                <div class="list-box">
                    <h3>📊 ক্যাটাগরি ভিত্তিক খরচ</h3>
                    <?php if(count($categoryData) > 0): ?>
                    <table>
                        <tr><th>ক্যাটাগরি</th><th>মোট খরচ (৳)</th></tr>
                        <?php foreach($categoryData as $cat): ?>
                        <tr>
                            <td><?php echo $cat['category']; ?></td>
                            <td>৳ <?php echo number_format($cat['total'], 2); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                    <?php else: ?>
                    <p>এই মাসে এখনো কোনো খরচ হয়নি।</p>
                    <?php endif; ?>
                </div>

                <div class="list-box">
                    <h3>📋 বিস্তারিত লেনদেন</h3>
                    <?php if(count($expenses) > 0): ?>
                    <table>
                        <tr><th>আইটেম</th><th>ক্যাটাগরি</th><th>দাম (৳)</th><th>দোকান</th><th>তারিখ</th></tr>
                        <?php foreach($expenses as $exp): ?>
                        <tr>
                            <td><?php echo $exp['item_name']; ?></td>
                            <td><?php echo $exp['category']; ?></td>
                            <td><?php echo number_format($exp['price'], 2); ?></td>
                            <td><?php echo $exp['shop_name'] ?? '-'; ?></td>
                            <td><?php echo date('d M Y', strtotime($exp['bought_at'])); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                    <?php else: ?>
                    <p>এই মাসে এখনো কোনো খরচ হয়নি।</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/js/script.js"></script>
</body>
</html>