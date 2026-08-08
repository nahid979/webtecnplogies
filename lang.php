<?php
// config/lang.php - সম্পূর্ণ বাংলা (Language Switcher বাদ দেওয়া হয়েছে)

// সব টেক্সট বাংলায় (Flat Array - কোনো [bn][en] নেই)
$translations = [
    // Navigation
    'app_name' => '🛒 বাজার হিসাব',
    'dashboard' => '📊 ড্যাশবোর্ড',
    'shopping_list' => '📝 বাজার তালিকা',
    'expense_report' => '💰 খরচের হিসাব',
    'logout' => '🚪 লগআউট',
    'welcome' => '👋 স্বাগতম',
    
    // Auth
    'login' => 'লগইন করুন',
    'register' => 'রেজিস্টার করুন',
    'email' => 'ইমেইল',
    'password' => 'পাসওয়ার্ড',
    'remember_me' => 'আমাকে মনে রাখো',
    'no_account' => 'অ্যাকাউন্ট নেই?',
    'have_account' => 'ইতিমধ্যে অ্যাকাউন্ট আছে?',
    'register_now' => 'রেজিস্টার করুন',
    'login_now' => 'লগইন করুন',
    'your_name' => 'আপনার নাম',
    'email_or_password_wrong' => '❌ ইমেইল বা পাসওয়ার্ড ভুল!',
    'account_created' => '✅ অ্যাকাউন্ট তৈরি হয়েছে! লগইন করুন।',
    
    // Dashboard
    'this_month_expense' => 'এই মাসের খরচ',
    'pending_items' => 'কিনতে বাকি',
    'expiring_soon' => '⏳ মেয়াদ শেষের কাছাকাছি',
    'no_pending_items' => '🎉 কোনো পেন্ডিং আইটেম নেই!',
    'expiry_alert' => '⚠️ সতর্কতা! নিচের জিনিসগুলোর মেয়াদ শেষ হতে ৩ দিন বাকি:',
    'expiry_date' => 'মেয়াদ',
    
    // Shopping List
    'add_new_item' => '➕ নতুন আইটেম যোগ করুন',
    'item_name' => 'আইটেমের নাম',
    'quantity' => 'পরিমাণ',
    'category' => 'ক্যাটাগরি',
    'vegetables' => 'সবজি',
    'fish_meat' => 'মাছ-মাংস',
    'groceries' => 'মুদি',
    'fruits' => 'ফল',
    'dairy' => 'দুগ্ধজাত',
    'others' => 'অন্যান্য',
    'add_button' => 'যোগ করুন',
    'pending' => '⏳ কিনতে বাকি আছে',
    'bought_history' => '✅ কেনা হয়েছে',
    'action' => 'অ্যাকশন',
    'bought' => '✅ কিনেছি',
    'delete' => '🗑️',
    'no_items_yet' => '🎉 তালিকা ফাঁকা! কিছু যোগ করুন।',
    'no_bought_yet' => 'এখনো কিছু কেনা হয়নি।',
    'item_added' => '✅ আইটেম যোগ হয়েছে!',
    'add_failed' => '❌ যোগ করতে ব্যর্থ!',
    'confirm_delete' => 'এই আইটেমটি ডিলিট করবেন?',
    
    // Buy Modal
    'enter_price_shop' => '✅ দাম ও দোকানের তথ্য দিন',
    'price' => 'দাম (টাকা)',
    'shop_name' => 'দোকানের নাম',
    'expiry_date_label' => 'মেয়াদ শেষের তারিখ',
    'bought_success' => '✅ কেনা হয়েছে! হিসাবে যোগ হয়েছে।',
    'bought_failed' => '❌ ব্যর্থ!',
    
    // Expense Report
    'monthly_expense' => 'এই মাসের খরচের হিসাব',
    'total_expense' => 'মোট খরচ',
    'category_wise' => '📊 ক্যাটাগরি ভিত্তিক খরচ',
    'total' => 'মোট খরচ (৳)',
    'transaction_details' => '📋 বিস্তারিত লেনদেন',
    'date' => 'তারিখ',
    'no_expense_yet' => 'এই মাসে এখনো কোনো খরচ হয়নি।',
    
    // Voice
    'voice_add' => '🎤 কথা বলে যোগ করুন',
    'voice_ready' => '⏸️ প্রস্তুত',
    'voice_listening' => '🎤 এখন কথা বলুন...',
    'voice_tip' => '💡 বলুন: "আমি আলু, ডিম, তেল আর চাল কিনব" (Chrome বা Edge ব্যবহার করুন)',
    'voice_error' => '❌ কিছুই বলেননি!',
    'voice_no_items' => '❌ কোনো আইটেম খুঁজে পাওয়া যায়নি!',
    
    // Language (যদি কোনো জায়গায় ল্যাঙ্গুয়েজ লেখা থাকে)
    'language' => '🌐 ভাষা',
    'bangla' => 'বাংলা',
    'english' => 'ইংরেজি'
];

// ট্রান্সলেশন ফাংশন - এখন ১০০% কাজ করবে
function __($key) {
    global $translations;
    
    // যদি কী-টা অ্যারেতে থাকে, তাহলে সেটা রিটার্ন করো
    if (isset($translations[$key])) {
        return $translations[$key];
    }
    
    // না থাকলে কী-টাই দেখাও (ডিবাগের জন্য)
    return $key;
}

// বর্তমান ভাষা (সবসময় বাংলা)
function current_lang() {
    return 'bn';
}
?>