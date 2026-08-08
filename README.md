# 🛒 Bazar Hisab – Smart Shopping & Expense Tracker

[![PHP](https://img.shields.io/badge/PHP-7.4+-blue.svg)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-5.7+-orange.svg)](https://mysql.com)
[![JavaScript](https://img.shields.io/badge/JavaScript-ES6-yellow.svg)](https://developer.mozilla.org)
[![Voice](https://img.shields.io/badge/Voice-SpeechRecognition-brightgreen.svg)](https://developer.mozilla.org/en-US/docs/Web/API/SpeechRecognition)
[![Security](https://img.shields.io/badge/Security-Aware-red.svg)](https://owasp.org)

## Overview

Bazar Hisab is a full-stack MVC web application built using PHP, MySQL, AJAX, and Vanilla JavaScript to simplify household shopping and expense management. The application enables users to manage shopping lists, track expenses, monitor product expiry, and generate reports through an intuitive interface.

The project includes a voice-based shopping list feature using the Web Speech API, supporting Bangla and Banglish commands for hands-free item entry.

🔊 *"Ami 2 kg aloo, 1 dozen dim, ar 2 litre tel kinbo"* – Just speak, and it's added!

---

## ✨ Key Features (Why This Project Stands Out)

### 🎤 Intelligent Voice Command System
- Integrated the **Web Speech API** with a custom **Bangla/Banglish parsing algorithm**.
- Extracts both **item names and quantities** from unstructured voice inputs (e.g., "aloo 2 kg", "ডিম ১ ডজন", "আমি ৩টা পেঁপে নিচি").
- Makes the app **accessible to non-tech-savvy users**, including elderly family members or those with limited typing ability.
- A **standout feature** rarely implemented in academic or personal projects, demonstrating proficiency in **API integration, string manipulation, and localization**.

### 📄 One-Click PDF & Print Reports
- Built a **business-ready reporting module** using **CSS print media queries**.
- Allows users to generate **professional, print-ready expense reports** with a single click.
- Perfect for maintaining **physical records, sharing with family members, or submitting to financial advisors**.
- Eliminates the need for **third-party tools or manual copy-pasting**.

### 📊 Smart Expense Tracking & Analytics
- Automatically logs expenses when items are marked as "**Bought**".
- Generates **real-time category-wise expense summaries** (Vegetables, Fish & Meat, Groceries, Fruits, Dairy).
- Interactive dashboard with **monthly spending trends** and **proactive 3-day expiry alerts** to minimize household food waste.
- Helps users answer critical questions: *"Where did my money go this month?"* and *"What's about to expire?"*

### ⚡ Real-Time AJAX Operations
- All CRUD operations (**Add, Edit, Delete, Buy**) happen **without full page reloads**.
- Built using **Vanilla JavaScript, XMLHttpRequest, and JSON**.
- Provides a **seamless, app-like user experience** that feels modern and responsive.

### 🔒 Security-First Design
- **Password Security**: bcrypt hashing for safe password storage.
- **SQL Injection Prevention**: All database queries use **Prepared Statements** with parameterized queries.
- **XSS Protection**: All user-generated outputs are escaped using `htmlspecialchars()`.
- **Session Management**: Secure PHP Sessions with cookie-based "Remember Me" functionality.
- Demonstrates a **strong commitment to writing production-quality, secure code**—a trait highly valued in professional environments.

---

## 🧰 Technologies Used

| Category | Technologies |
| :--- | :--- |
| **Backend** | Pure PHP 7.4+, MySQLi (Prepared Statements) |
| **Frontend** | HTML5, CSS3, Vanilla JavaScript, AJAX (XMLHttpRequest), JSON |
| **Architecture** | Custom MVC (Model-View-Controller) without any frameworks |
| **Database** | MySQL with Foreign Key constraints and optimized JOIN queries |
| **APIs** | Web Speech API (Voice Recognition), JSON |
| **Security** | bcrypt hashing, Session Management, Prepared Statements, XSS Prevention |
| **Server** | XAMPP (Apache, MySQL) |

---

## 📁 Project Structure (MVC)

```text
bazar-hisab/
├── assets/
│   ├── css/
│   │   └── style.css          # Custom styling
│   └── js/
│       ├── script.js          # CRUD AJAX operations
│       └── voice.js           # Speech Recognition & parsing
├── config/
│   ├── database.php           # Database connection
│   └── lang.php               # Multi-language support (Bangla/English)
├── controllers/
│   ├── AuthController.php     # Login, Register, Logout
│   ├── DashboardController.php
│   ├── ListController.php     # Shopping list CRUD
│   ├── ExpenseController.php  # Expense reporting
│   └── VoiceController.php    # Voice input parsing algorithm
├── models/
│   ├── UserModel.php
│   ├── ListModel.php
│   └── ExpenseModel.php
├── views/
│   ├── auth/                  # Login / Register pages
│   ├── partials/              # Reusable components
│   ├── dashboard.php
│   ├── list.php               # Shopping list with Buy/Edit/Delete
│   └── expenses.php           # Expense report with PDF/Print
├── index.php                  # Main router
└── .htaccess                  # URL rewriting


## 🗄️ Database Tables (SQL Schema)

```sql
CREATE TABLE users (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE shopping_list (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    user_id INT(11) NOT NULL,
    item_name VARCHAR(100) NOT NULL,
    quantity VARCHAR(50),
    category VARCHAR(50),
    status ENUM('pending', 'bought') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE expenses (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    user_id INT(11) NOT NULL,
    item_id INT(11) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    shop_name VARCHAR(100),
    expiry_date DATE,
    bought_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (item_id) REFERENCES shopping_list(id) ON DELETE CASCADE
);

## Screenshots





## Future Improvements

- Email notifications
- Barcode scanning
- OCR for receipts
- Progressive Web App (PWA)
- Budget forecasting

## Learning Outcomes

Through this project I gained practical experience in:

- MVC Architecture
- REST-like application design
- AJAX
- PHP Session Management
- MySQL Database Design
- Web Security
- API Integration

## 📬 Let's Connect

Found this interesting? I'd love to hear your feedback or discuss potential collaborations.  
Feel free to reach out!
[LinkedIn](https://www.linkedin.com/in/udaydey/)
