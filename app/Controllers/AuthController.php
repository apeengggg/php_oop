<?php
require __DIR__.'/../../config/Database.php';
require __DIR__.'/../Models/AuthModel.php';
require __DIR__.'/../Middlewares/Jwt.php';

class AuthController{
    public function index(){
        include __DIR__. '/../Views/Login/index.php';
    }
    
    public function login(){
        $username = $_POST['username'];
        $password = $_POST['password'];
        
        $auth = new Auth(new Database());
        $result = $auth->login($username);
        if ($result) {
            $hashedPassword = $result['password'];
            $valid = password_verify($password, $hashedPassword);
            if ($valid) {
                if (isset($result['password'])) {
                    unset($result['password']);
                }
                $token = new Token();
                $jwt = $token->generateToken($result);

                $permission = $auth->getPermission($result['role_id']);
                $result['permission'] = $permission;

                $_SESSION['token'] = $jwt;
                $_SESSION['user'] = $result;
                echo json_encode(['status' => 200, 'message' => 'Login Successfully', 'data' => $result, 'token' => $jwt]);
            }else{
                echo json_encode(['status' => 400, 'message' => 'Login Failed']);
            }
        }else{
            echo json_encode(['status' => 400, 'message' => 'Login Failed']);
        } 
    }
}