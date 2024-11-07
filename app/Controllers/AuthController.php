<?php
require __DIR__.'/../../config/Database.php';
require __DIR__.'/../Models/AuthModel.php';
require __DIR__.'/../Middlewares/Jwt.php';

class AuthController{
    protected $response;

    public function __construct()
    {
        $this->response = new Response();
    }

    public function index(){
        include __DIR__. '/../Views/Login/index.php';
    }
    
    public function login(){
        header('Content-Type: application/json');
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
                echo $this->response->OkLogin($result, 'Login Successfully', $jwt);
            }else{
                echo $this->response->BadRequest("Login Failed");
            }
        }else{
            echo $this->response->BadRequest("Login Failed");
        } 
    }

    public function notfound(){
        include __DIR__. '/../Views/Error/404.php';
    }

    public function forbidden(){
        include __DIR__. '/../Views/Error/403.php';
    }

    public function logout(){
        $_SESSION = [];
        session_destroy();
        echo $this->response->Success("Logout Successfully");
    }
}