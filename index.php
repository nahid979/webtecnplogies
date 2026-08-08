<?php
// index.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'controllers/AuthController.php';
require_once 'controllers/DashboardController.php';
require_once 'controllers/ListController.php';
require_once 'controllers/ExpenseController.php';
require_once 'controllers/VoiceController.php';

$action = $_GET['action'] ?? 'login';

switch ($action) {
    case 'login':
        $controller = new AuthController();
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $controller->login();
        } else {
            $controller->showLogin();
        }
        break;
        
    case 'register':
        $controller = new AuthController();
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $controller->register();
        } else {
            $controller->showRegister();
        }
        break;
        
    case 'logout':
        $controller = new AuthController();
        $controller->logout();
        break;
        
    case 'dashboard':
        $controller = new DashboardController();
        $controller->index();
        break;
        
    case 'list':
        $controller = new ListController();
        $controller->index();
        break;
        
    case 'addItem':
        $controller = new ListController();
        $controller->addItem();
        break;
        
    case 'deleteItem':
        $controller = new ListController();
        $controller->deleteItem();
        break;
        
    case 'markBought':
        $controller = new ListController();
        $controller->markBought();
        break;
        
    case 'getPending':
        $controller = new ListController();
        $controller->getPending();
        break;
        
    case 'expenses':
        $controller = new ExpenseController();
        $controller->index();
        break;
        
    case 'voiceAdd':
        $controller = new VoiceController();
        $controller->voiceAdd();
        break;
        

    case 'editItem':
        $controller = new ListController();
        $controller->editItem();
        break;
        
    case 'getItem':
        $controller = new ListController();
        $controller->getItem();
        break;
        
    default:
        echo "404 - পেজ খুঁজে পাওয়া যায়নি!";
        break;
}
?>