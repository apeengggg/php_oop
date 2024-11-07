<?php

class Response{
    public function createResponse($status_code, $msg, $data=null, $error=null, $totalPages=null, $token=null){
        $response = ['status' => $status_code, 'message' => $msg];
        if (!empty($error)) {
            $response['error'] = $error;
        }
        
        if (!empty($data)) {
            $response['data'] = $data;
        }

        if (!empty($totalPages)) {
            $response['totalPages'] = $totalPages;
        }

        if (!empty($token)) {
            $response['token'] = $token;
        }


        return json_encode($response);
    }

    public function Ok($data, $msg){
        return $this->createResponse(200, $msg, $data);
    }

    public function OkPaging($data, $msg, $totalPages){
        return $this->createResponse(200, $msg, $data, null, $totalPages);
    }

    public function OkLogin($data, $msg, $token){
        return $this->createResponse(200, $msg, $data, null, null, $token);
    }

    public function Success($msg){
        return $this->createResponse(200, $msg);
    }

    public function BadRequest($msg){
        return $this->createResponse(400, $msg, null, "Bad Request");
    }

    public function InternalServerError($error){
        return $this->createResponse(500, "Internal Server Error", null, $error);
    }

    public function ForbiddenAccess($msg){
        return $this->createResponse(403, $msg);
    }

    public function Unauthorization($msg){
        return $this->createResponse(401, $msg);
    }
}