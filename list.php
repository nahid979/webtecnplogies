<?php
// views/list.php
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?action=login');
    exit;
}

require_once 'models/ListModel.php';
require_once 'models/ExpenseModel.php';

$user_id = $_SESSION['user_id'];
$listModel = new ListModel();
$pendingItems = $listModel->getPendingItems($user_id);
$boughtItems = $listModel->getBoughtItems($user_id);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>বাজার তালিকা - বাজার হিসাব</title>
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
            <p>👋 স্বাগতম <?php echo htmlspecialchars($_SESSION['user_name']); ?></p>
            <hr>
            <a href="index.php?action=dashboard">📊 ড্যাশবোর্ড</a>
            <a href="index.php?action=list">📝 বাজার তালিকা</a>
            <a href="index.php?action=expenses">💰 খরচের হিসাব</a>
            <a href="index.php?action=logout">🚪 লগআউট</a>
        </div>

        <div class="main-content">
            <h2>📝 বাজার তালিকা</h2>

            <!-- Voice Section -->
            <div class="voice-section" style="background: #0a111a; padding: 15px; border-radius: 8px; margin-bottom: 15px; border: 1px dashed #00d4ff;">
                <div style="display: flex; align-items: center; gap: 15px; flex-wrap: wrap;">
                    <button id="voiceBtn" onclick="startVoiceInput()" class="btn btn-primary" style="padding: 12px 25px; font-size: 16px;">
                        🎤 কথা বলে যোগ করুন
                    </button>
                    <span id="voiceStatus" style="color: #8899bb; font-size: 14px;">⏸️ প্রস্তুত</span>
                </div>
                <p style="color: #8899bb; font-size: 12px; margin-top: 10px;">
                    💡 বলুন: "আমি ২টা আলু, ১ কেজি ডিম, ২ লিটার তেল কিনব" 
                </p>
            </div>

            <!-- Add Form -->
            <div class="form-box">
                <h3>➕ নতুন আইটেম যোগ করুন</h3>
                <form id="addForm">
                    <input type="text" name="item_name" id="item_name" placeholder="আইটেমের নাম" required>
                    <input type="text" name="quantity" id="quantity" placeholder="পরিমাণ (১ কেজি)">
                    <select name="category" id="category">
                        <option value="সবজি">সবজি</option>
                        <option value="মাছ-মাংস">মাছ-মাংস</option>
                        <option value="মুদি">মুদি</option>
                        <option value="ফল">ফল</option>
                        <option value="দুগ্ধজাত">দুগ্ধজাত</option>
                        <option value="অন্যান্য">অন্যান্য</option>
                    </select>
                    <button type="submit" class="btn btn-primary">যোগ করুন</button>
                </form>
                <div id="msg"></div>
            </div>

            <!-- Pending List -->
            <div class="list-box">
                <h3>⏳ কিনতে বাকি আছে</h3>
                <div id="pendingList">
                    <?php if(count($pendingItems) > 0): ?>
                    <table>
                        <tr><th>আইটেম</th><th>পরিমাণ</th><th>ক্যাটাগরি</th><th>অ্যাকশন</th></tr>
                        <?php foreach($pendingItems as $item): ?>
                        <tr id="row_<?php echo $item['id']; ?>">
                            <!-- ========================================== -->
                            <!-- 🔥 এখানে htmlspecialchars() যোগ করা হয়েছে (XSS বন্ধ করতে) -->
                            <!-- ========================================== -->
                            <td><?php echo htmlspecialchars($item['item_name']); ?></td>
                            <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                            <td><?php echo htmlspecialchars($item['category']); ?></td>
                            <td>
                                <button onclick="showBuyForm(<?php echo $item['id']; ?>)" class="btn btn-success">✅ কিনেছি</button>
                                <button onclick="showEditForm(<?php echo $item['id']; ?>)" class="btn btn-primary">✏️ এডিট</button>
                                <button onclick="deleteItem(<?php echo $item['id']; ?>)" class="btn btn-danger">🗑️</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                    <?php else: ?>
                    <p>🎉 তালিকা ফাঁকা! কিছু যোগ করুন।</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Bought History -->
            <div class="list-box">
                <h3>✅ কেনা হয়েছে</h3>
                <?php if(count($boughtItems) > 0): ?>
                <table>
                    <tr><th>আইটেম</th><th>পরিমাণ</th><th>ক্যাটাগরি</th></tr>
                    <?php foreach($boughtItems as $item): ?>
                    <tr>
                        <!-- 🔥 এখানেও htmlspecialchars() যোগ করা হয়েছে -->
                        <td><?php echo htmlspecialchars($item['item_name']); ?></td>
                        <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                        <td><?php echo htmlspecialchars($item['category']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
                <?php else: ?>
                <p>এখনো কিছু কেনা হয়নি।</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Buy Modal -->
    <div id="buyModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.7); z-index:999;">
        <div style="background:#141e2b; max-width:400px; margin:100px auto; padding:30px; border-radius:10px; border:1px solid #1a2a3a;">
            <span onclick="closeBuyForm()" style="float:right; cursor:pointer; color:#fff; font-size:24px;">&times;</span>
            <h3>✅ দাম ও দোকানের তথ্য দিন</h3>
            <form id="buyForm">
                <input type="hidden" name="item_id" id="buy_item_id">
                <div class="form-group">
                    <label>দাম (টাকা)</label>
                    <input type="number" step="0.01" name="price" id="price" required>
                </div>
                <div class="form-group">
                    <label>দোকানের নাম</label>
                    <input type="text" name="shop_name" id="shop_name">
                </div>
                <div class="form-group">
                    <label>মেয়াদ শেষের তারিখ</label>
                    <input type="date" name="expiry_date" id="expiry_date">
                </div>
                <button type="submit" class="btn btn-success">✅ কিনেছি</button>
            </form>
            <div id="buyMsg"></div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.7); z-index:999;">
        <div style="background:#141e2b; max-width:400px; margin:100px auto; padding:30px; border-radius:10px; border:1px solid #1a2a3a;">
            <span onclick="closeEditForm()" style="float:right; cursor:pointer; color:#fff; font-size:24px;">&times;</span>
            <h3>✏️ আইটেম এডিট করুন</h3>
            <form id="editForm">
                <input type="hidden" name="item_id" id="edit_item_id">
                <div class="form-group">
                    <label>আইটেমের নাম</label>
                    <input type="text" name="item_name" id="edit_item_name" required>
                </div>
                <div class="form-group">
                    <label>পরিমাণ</label>
                    <input type="text" name="quantity" id="edit_quantity">
                </div>
                <div class="form-group">
                    <label>ক্যাটাগরি</label>
                    <select name="category" id="edit_category">
                        <option value="সবজি">সবজি</option>
                        <option value="মাছ-মাংস">মাছ-মাংস</option>
                        <option value="মুদি">মুদি</option>
                        <option value="ফল">ফল</option>
                        <option value="দুগ্ধজাত">দুগ্ধজাত</option>
                        <option value="অন্যান্য">অন্যান্য</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">💾 আপডেট করুন</button>
            </form>
            <div id="editMsg"></div>
        </div>
    </div>

    <script>
        var langData = {
            confirm_delete: 'এই আইটেমটি ডিলিট করবেন?',
            item_added: 'আইটেম যোগ হয়েছে!',
            add_failed: 'যোগ করতে ব্যর্থ!',
            bought_success: 'কেনা হয়েছে! হিসাবে যোগ হয়েছে।',
            bought_failed: 'ব্যর্থ!'
        };
    </script>
    <script src="assets/js/script.js"></script>
    <script src="assets/js/voice.js"></script>
    <script>
       
        // ============================
        function showEditForm(id) {
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'index.php?action=getItem&item_id=' + id, true);
            xhr.onload = function() {
                if (this.status == 200) {
                    var response = JSON.parse(this.responseText);
                    if (response.success) {
                        var item = response.item;
                        document.getElementById('edit_item_id').value = item.id;
                        document.getElementById('edit_item_name').value = item.item_name;
                        document.getElementById('edit_quantity').value = item.quantity || '';
                        document.getElementById('edit_category').value = item.category;
                        document.getElementById('editModal').style.display = 'block';
                    }
                }
            };
            xhr.send();
        }

        function closeEditForm() {
            document.getElementById('editModal').style.display = 'none';
            document.getElementById('editMsg').innerHTML = '';
        }

        document.getElementById('editForm').addEventListener('submit', function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'index.php?action=editItem', true);
            xhr.onload = function() {
                if (this.status == 200) {
                    var response = JSON.parse(this.responseText);
                    if (response.success) {
                        document.getElementById('editMsg').innerHTML = '<div class="alert alert-success">✅ আইটেম আপডেট হয়েছে!</div>';
                        setTimeout(function() {
                            closeEditForm();
                            loadPendingList();
                        }, 1000);
                    } else {
                        document.getElementById('editMsg').innerHTML = '<div class="alert alert-danger">❌ আপডেট ব্যর্থ!</div>';
                    }
                }
            };
            xhr.send(formData);
        });

        window.onclick = function(event) {
            var buyModal = document.getElementById('buyModal');
            var editModal = document.getElementById('editModal');
            if (event.target == buyModal) {
                buyModal.style.display = 'none';
            }
            if (event.target == editModal) {
                editModal.style.display = 'none';
            }
        }
    </script>
</body>
</html>