<?php
require '../../config/Database.php';
require '../Middlewares/Jwt.php';
require '../Services/AuthService.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $login = new AuthService(new Database(), new Token());
    echo $login->login($username, $password);
}