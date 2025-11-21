<?php
/**
 * Main Entry Point - index.php
 * E-Commerce Application
 */

// Start session only once
session_start();

// Load configuration
require_once __DIR__ . '/config/database.php';

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Initialize user session
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = null;
}

// Router - Handle different pages
$page = isset($_GET['page']) ? $_GET['page'] : 'products';
$action = isset($_GET['action']) ? $_GET['action'] : '';

// Route handling
switch ($page) {
    case 'products':
        require_once __DIR__ . '/controllers/ProductController.php';
        $controller = new ProductController($conn);
        $controller->index();
        break;
        
    case 'cart':
        require_once __DIR__ . '/controllers/CartController.php';
        $controller = new CartController($conn);
        
        if ($action === 'add') {
            $controller->add();
        } elseif ($action === 'update') {
            $controller->update();
        } elseif ($action === 'remove') {
            $controller->remove();
        } else {
            $controller->view();
        }
        break;
        
    case 'checkout':
        require_once __DIR__ . '/controllers/OrderController.php';
        $controller = new OrderController($conn);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->placeOrder();
        } else {
            $controller->showCheckout();
        }
        break;
        
    case 'order-success':
        require_once __DIR__ . '/views/order-success.php';
        break;
        
    case 'admin':
        $adminPage = isset($_GET['view']) ? $_GET['view'] : 'login';
        
        require_once __DIR__ . '/controllers/AdminController.php';
        $controller = new AdminController($conn);
        
        switch ($adminPage) {
            case 'login':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $controller->login();
                } else {
                    $controller->showLogin();
                }
                break;
                
            case 'dashboard':
                $controller->dashboard();
                break;
                
            case 'orders':
                $controller->orders();
                break;
                
            case 'logout':
                $controller->logout();
                break;
                
            default:
                $controller->showLogin();
        }
        break;
        
    default:
        // Default to products page
        require_once __DIR__ . '/controllers/ProductController.php';
        $controller = new ProductController($conn);
        $controller->index();
        break;
}
?>
