<?php

require 'vendor/autoload.php'; // Include Composer's autoloader
require 'config/bootstrap.php';

use Firebase\JWT\JWT;
use Firebase\JWT\ExpiredException;

class Token
{
    private $secretKey = JWT_SECRET_KEY;

    public function handle($request)
    {
        if (isset($request['headers']['Authorization'])) {
            $authHeader = $request['headers']['Authorization'];
            list($jwt) = sscanf($authHeader, 'Bearer %s');

            if ($jwt) {
                try {
                    $decoded = JWT::decode($jwt, $this->secretKey, ['HS256']);
                    $request['user'] = (array) $decoded->data;
                    return true;
                } catch (ExpiredException $e) {
                    return false;
                } catch (Exception $e) {
                    return false;
                }
            }
        }
        return false;
    }
}
