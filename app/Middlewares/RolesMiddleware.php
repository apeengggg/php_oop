<?php
session_start();
require __DIR__. '/../../vendor/autoload.php';
require __DIR__. '/../Models/PermissionModel.php';


class RolesMiddleware
{
    protected $permissionModel;
    protected $response;

    public function __construct($db){
        $this->permissionModel = new Permission($db);
        $this->response = new Response();
    }

    public function handle($function_id, $access_name)
    {
        $user_data = $_SESSION['user'];
        $permissions = $this->permissionModel->permissionByRole($user_data['user_id'], $function_id);
        if(empty($permissions) || $permissions[$access_name] === '0'){
            echo $this->response->ForbiddenAccess("Access Denied");
            exit;
        }
    }
}
