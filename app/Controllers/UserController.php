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
        try{
            $data = $this->userModel->all($param);

            header('Content-Type: application/json');
            echo json_encode(['status' => 200, 'data'=> $data, 'message'=> 'Success Get Data']);
        }catch(\Exception $e){
            echo json_encode(['status' => 500, 'message' => 'Internal Server Error', 'error' => $e->getMessage()]);

        }
    }

    public function destroy(){
        if(!isset($_POST['user_id'])){
            echo json_encode(['status' => 400, 'message' => 'User Id Required']);
            return;
        }

        try{
            $delete = $this->userModel->destroy($_POST['user_id']);
            if($delete){
                echo json_encode(['status' => 200, 'message'=> 'Success Delete Data']);
            }else{
                echo json_encode(['status' => 200, 'message'=> 'Error Delete Data']);
            }
        }catch(\Exception $e){
            echo json_encode(['status' => 500, 'message'=> 'Internal Server Error', 'error' => $e->getMessage()]);
        }
    }


}