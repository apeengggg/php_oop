<?php
require_once __DIR__.'/../app/Controllers/AuthController.php';
require_once __DIR__.'/../app/Controllers/UserController.php';
require_once __DIR__.'/../app/Controllers/EventController.php';
require_once __DIR__.'/../app/Middlewares/Jwt.php';
require_once __DIR__.'/../app/Middlewares/RolesMiddleware.php';
require_once __DIR__.'/../config/Database.php';

$uri;

$db = (new Database())->getConnection();

$authController = new AuthController();
$userController = new UserController($db);
$eventController = new EventController($db);
$roleMiddleware = new RolesMiddleware($db);
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
    case '/403':
        $authController->forbidden();
        break;
    case '/login/authenticate':
        $authController->login();
        break;
    case '/users':
        $userController->index();
        break;
    case '/users/get':
        $jwt->handle();
        $roleMiddleware->handle('M001', 'can_read');
        $userController->get();
        break;
    case '/users/post':
        $jwt->handle();
        $roleMiddleware->handle('M001', 'can_create');
        $userController->store();
        break;
    case '/users/put':
        $jwt->handle();
        $roleMiddleware->handle('M001', 'can_update');
        $userController->update();
        break;
    case '/user/delete':
        $jwt->handle();
        $roleMiddleware->handle('M001', 'can_delete');
        $userController->destroy();
        break;
    case '/events':
        $eventController->index();
        break;
    case '/event/get':
        $jwt->handle();
        $roleMiddleware->handle('M003', 'can_read');
        $eventController->get();
        break;
    case '/event/post':
        $jwt->handle();
        $roleMiddleware->handle('M003', 'can_create');
        $eventController->store();
        break;
    case '/event/put':
        $jwt->handle();
        $roleMiddleware->handle('M003', 'can_update');
        $eventController->update();
        break;
    case '/event/delete':
        $jwt->handle();
        $roleMiddleware->handle('M003', 'can_delete');
        $eventController->destroy();
        break;
    default:
        $authController->notfound();
    
}