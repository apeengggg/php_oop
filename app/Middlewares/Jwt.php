<?php

require __DIR__. '/../../vendor/autoload.php';
require __DIR__. '/../../config/bootstrap.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;

class Token
{
    private $secretKey = JWT_SECRET_KEY;

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
                    echo json_encode(["status" => 401, 'message' => "Token has expired."]);
                    exit;
                } catch (Exception $e) {
                    echo json_encode(["status" => 401, 'message' => "Invalid token."]);
                    exit;
                }
            } else {
                echo json_encode(["status" => 401, 'message' => "Authorization header format is invalid."]);
                exit;
            }
        } else {
            echo json_encode(["status" => 401, 'message' => "Authorization header not found."]);
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

    public function decodeToken($token) {
        try {
            if($token == null){
                return false;
            }

            return JWT::decode($token, new Key($this->secretKey, 'HS256'));
        } catch (ExpiredException $e) {
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }
}
