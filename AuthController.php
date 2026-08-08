<?php
// controllers/AuthController.php
require_once 'models/UserModel.php';

class AuthController {
    private $model;
    
    public function __construct() {
        $this->model = new UserModel();
    }
    
    public function showLogin() {
        include 'views/auth/login.php';
    }
    
    public function showRegister() {
        include 'views/auth/register.php';
    }
    
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = $_POST['name'];
            $email = $_POST['email'];
            $password = $_POST['password'];
            
            if ($this->model->register($name, $email, $password)) {
                header('Location: index.php?action=login&registered=1');
            } else {
                echo "রেজিস্ট্রেশন ব্যর্থ!";
            }
        }
    }
    
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = $_POST['email'];
            $password = $_POST['password'];
            $user = $this->model->login($email, $password);
            
            if ($user) {
                session_start();
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                
                if (isset($_POST['remember'])) {
                    setcookie('user_email', $email, time() + (86400 * 30), "/");
                }
                
                header('Location: index.php?action=dashboard');
            } else {
                header('Location: index.php?action=login&error=1');
            }
        }
    }
    
    public function logout() {
        session_start();
        session_destroy();
        setcookie('user_email', '', time() - 3600, "/");
        header('Location: index.php?action=login');
    }
}
?>