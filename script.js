// ====================================
// DOM লোড হওয়ার পর সবকিছু রান করবে
// ====================================
document.addEventListener('DOMContentLoaded', function() {
    
    // ---------- আইটেম যোগ ফর্ম (শুধু list.php পেজে থাকলে) ----------
    var addForm = document.getElementById('addForm');
    if (addForm) {
        addForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            var formData = new FormData(this);
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'index.php?action=addItem', true);
            xhr.onload = function() {
                if (this.status == 200) {
                    var response = JSON.parse(this.responseText);
                    var msgDiv = document.getElementById('msg');
                    if (response.success) {
                        msgDiv.innerHTML = '<div class="alert alert-success">✅ ' + (langData.item_added || 'আইটেম যোগ হয়েছে!') + '</div>';
                        document.getElementById('addForm').reset();
                        if (typeof loadPendingList === 'function') {
                            loadPendingList();
                        }
                    } else {
                        msgDiv.innerHTML = '<div class="alert alert-danger">❌ ' + (langData.add_failed || 'যোগ করতে ব্যর্থ!') + '</div>';
                    }
                }
            };
            xhr.send(formData);
        });
    }
    
    // ---------- কেনার ফর্ম (শুধু list.php পেজে থাকলে) ----------
    var buyForm = document.getElementById('buyForm');
    if (buyForm) {
        buyForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            var formData = new FormData(this);
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'index.php?action=markBought', true);
            xhr.onload = function() {
                if (this.status == 200) {
                    var response = JSON.parse(this.responseText);
                    var buyMsg = document.getElementById('buyMsg');
                    if (response.success) {
                        buyMsg.innerHTML = '<div class="alert alert-success">✅ ' + (langData.bought_success || 'কেনা হয়েছে!') + '</div>';
                        setTimeout(function() {
                            closeBuyForm();
                            if (typeof loadPendingList === 'function') {
                                loadPendingList();
                            }
                            document.getElementById('buyForm').reset();
                            buyMsg.innerHTML = '';
                        }, 1500);
                    } else {
                        buyMsg.innerHTML = '<div class="alert alert-danger">❌ ' + (langData.bought_failed || 'ব্যর্থ!') + '</div>';
                    }
                }
            };
            xhr.send(formData);
        });
    }
});

// ====================================
// পেন্ডিং লিস্ট লোড (AJAX)
// ====================================
function loadPendingList() {
    var container = document.getElementById('pendingList');
    if (!container) return; // যদি পেজে pendingList না থাকে, তাহলে কিছু করো না
    
    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'index.php?action=getPending', true);
    xhr.onload = function() {
        if (this.status == 200) {
            var data = JSON.parse(this.responseText);
            
            if (data.length > 0) {
                var html = '<table><tr><th>আইটেম</th><th>পরিমাণ</th><th>ক্যাটাগরি</th><th>অ্যাকশন</th></tr>';
                data.forEach(function(item) {
                    html += '<tr id="row_' + item.id + '">';
                    html += '<td>' + item.item_name + '</td>';
                    html += '<td>' + (item.quantity || '') + '</td>';
                    html += '<td>' + item.category + '</td>';
                    html += '<td>';
                    html += '<button onclick="showBuyForm(' + item.id + ')" class="btn btn-success">✅ কিনেছি</button> ';
                    html += '<button onclick="deleteItem(' + item.id + ')" class="btn btn-danger">🗑️</button>';
                    html += '</td></tr>';
                });
                html += '</table>';
                container.innerHTML = html;
            } else {
                container.innerHTML = '<p>🎉 তালিকা ফাঁকা! কিছু যোগ করুন।</p>';
            }
        }
    };
    xhr.send();
}

// ====================================
// আইটেম ডিলিট (AJAX)
// ====================================
function deleteItem(id) {
    if (!confirm(langData.confirm_delete || 'এই আইটেমটি ডিলিট করবেন?')) return;
    
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'index.php?action=deleteItem', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        if (this.status == 200) {
            var response = JSON.parse(this.responseText);
            if (response.success) {
                var row = document.getElementById('row_' + id);
                if (row) row.remove();
            }
        }
    };
    xhr.send('item_id=' + id);
}

// ====================================
// কেনার ফর্ম দেখানো
// ====================================
function showBuyForm(id) {
    var modal = document.getElementById('buyModal');
    if (!modal) return;
    
    document.getElementById('buy_item_id').value = id;
    modal.style.display = 'block';
}

function closeBuyForm() {
    var modal = document.getElementById('buyModal');
    if (modal) modal.style.display = 'none';
}

// ====================================
// মডালের বাইরে ক্লিক করলে বন্ধ
// ====================================
window.onclick = function(event) {
    var modal = document.getElementById('buyModal');
    if (event.target == modal) {
        modal.style.display = 'none';
    }
}