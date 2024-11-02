<?php
$uri;

if(isset($_SERVER['PATH_INFO'])){
    $uri = $_SERVER['PATH_INFO'];
}else{
    $uri = '/';
}

switch ($uri){
    case '/':
        include __DIR__. '/../app/Views/Login/index.php';
        break;
    case '/admin/dashboard':
        include __DIR__. '/../app/Views/Admin/index.php';
        break;
    default:
        break;
    
}