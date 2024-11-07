<?php

require __DIR__. '/../../vendor/autoload.php';
require __DIR__. '/../../config/bootstrap.php';
require __DIR__. '/../Helpers/Response.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;

class Token
{
    private $secretKey = JWT_SECRET_KEY;
    protected $response;

    public function __construct()
    {
        $this->response = new Response();
    }

    public function handle()
    {
        header('Content-Type: application/json');
        $headers = apache_request_headers();
        if (isset($headers['Authorization'])) {
            $authHeader = $headers['Authorization'];
            
            if (sscanf($authHeader, 'Bearer %s', $jwt) === 1) {
                try {
                    $decoded = JWT::decode($jwt, new Key($this->secretKey, 'HS256'));
                    $request['user'] = (array) $decoded->data;
                } catch (ExpiredException $e) {
                    echo $this->response->Unauthorization("Token Expired");
                    exit;
                } catch (Exception $e) {
                    echo $this->response->Unauthorization("Invalid Token");
                    exit;
                }
            } else {
                echo $this->response->Unauthorization("Authorization Header Format Is Invalid");
                exit;
            }
        } else {
            echo $this->response->Unauthorization("Token Not Found");
            exit;
        }
    }

    public function generateToken($data) {
        $payload = [
            'iat' => time(),
            'exp' => (time() + 3600) * 24,
            'iss' => 'pmpland',
            'data' => $data,
        ];

        return JWT::encode($payload, $this->secretKey, 'HS256');
    }
}
