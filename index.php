<?php
require './vendor/autoload.php';
require './app/Middlewares/Jwt.php';
session_start();

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

class Index{
    protected $token;

    public function __construct($token){
        $this->token = $token;
    }

    function check_token(){
        $check = new Token();
        return $check->handle($this->token);
    }
}

$token;
if(isset($_SESSION['token'])){
    $token = $_SESSION['token'];
}else{
    $token = null;
}

$validate = new Index($token);
$hasLogin = $validate->check_token();

if(!$hasLogin || empty($token)){
    require_once 'app/Views/Login/index.php';
}else{
    require './routes/index.php';
}