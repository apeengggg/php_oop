<?php
session_start();
require __DIR__. '/../../vendor/autoload.php';
require __DIR__. '/../Models/PermissionModel.php';


class RolesMiddleware
{
    protected $permissionModel;

    public function __construct($db){
        $this->permissionModel = new Permission($db);
    }

    public function handle($function_id, $access_name)
    {
        $user_data = $_SESSION['user'];
        $permissions = $this->permissionModel->permissionByRole($user_data['user_id'], $function_id);
        // var_dump($permissions);
        // exit;
        if(empty($permissions) || $permissions[$access_name] === '0'){
            echo json_encode(["status" => 403, 'message' => "Forbidden Access"]);
            exit;
        }
    }
}