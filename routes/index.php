<?php
require_once __DIR__.'/../app/Controllers/AuthController.php';
require_once __DIR__.'/../app/Controllers/UserController.php';
require_once __DIR__.'/../config/Database.php';

$uri;

$db = (new Database())->getConnection();

$authController = new AuthController();
$userController = new UserController($db);

if(isset($_SERVER['PATH_INFO'])){
    $uri = $_SERVER['PATH_INFO'];
}else{
    $uri = '/';
}

switch ($uri){
    case '/':
        $authController->index();
        break;
    case '/login/authenticate':
        $authController->login();
        break;
    case '/admin/dashboard':
        include __DIR__. '/../app/Views/Admin/index.php';
        break;
    case '/admin/users':
        $userController->index();
        break;
    case '/users':
        $userController->get();
    default:
        break;
    
}