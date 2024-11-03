<?php
require_once __DIR__.'/../app/Controllers/AuthController.php';
require_once __DIR__.'/../app/Controllers/UserController.php';
require_once __DIR__.'/../app/Middlewares/Jwt.php';
require_once __DIR__.'/../config/Database.php';

$uri;

$db = (new Database())->getConnection();

$authController = new AuthController();
$userController = new UserController($db);
$jwt = new Token();

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
    case '/dashboard':
        include __DIR__. '/../app/Views/Admin/index.php';
        break;
    case '/users':
        $userController->index();
        break;
    case '/users/get':
        $jwt->handle();
        $userController->get();
        break;
    case '/users/post':
        $jwt->handle();
        $userController->store();
        break;
    case '/user/delete':
        $jwt->handle();
        $userController->destroy();
        break;
    default:
        break;
    
}