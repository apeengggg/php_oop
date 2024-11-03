<?php 

require_once __DIR__.'/../Models/UserModel.php';

class UserController {
    private $userModel;

    public function __construct($db)
    {
        $this->userModel = new User($db);
    }

    public function index(){
        include __DIR__.'/../Views/User/index.php';
    }

    public function get(){
        $param = $_GET;
        // var_dump($_GET);
        try{
            $data = $this->userModel->all($param);

            header('Content-Type: application/json');
            echo json_encode(['status' => 200, 'data'=> $data]);
        }catch(\Exception $e){
            echo json_encode(['status' => 500, 'message' => 'Internal Server Error', 'error' => $e->getMessage()]);

        }

    }


}