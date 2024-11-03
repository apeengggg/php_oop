<?php
require __DIR__.'/../../config/Database.php';
require __DIR__.'/../Services/AuthService.php';

class AuthController{
    public function index(){
        include __DIR__. '/../Views/Login/index.php';
    }
    
    public function login(){
        $username = $_POST['username'];
        $password = $_POST['password'];
        
        $login = new AuthService(new Database(), new Token());
        echo $login->login($username, $password);
    }
}