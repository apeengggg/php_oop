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

    public function store(){
        header('Content-Type: application/json');

        $body = $_POST;
        $filename = '';
        try{
            if(!empty($_FILES) || isset($_FILES['image'])){
                $file = $_FILES['image'];

                if ($file['error'] !== UPLOAD_ERR_OK) {
                    echo json_encode(['status' => 400, 'message'=> 'Error Uploading Image']);
                    exit;
                }

                $uploadDir = 'public/img/profile/';
                $uniqueName = uniqid() . '_' . basename($file['name']);
                $uploadFile = $uploadDir . $uniqueName;

                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                if (move_uploaded_file($file['tmp_name'], $uploadFile)) {
                    $filename = '../'.$uploadDir.$uniqueName;
                } else {
                    echo json_encode(['status' => 400, 'message'=> 'Error Uploading Image']);
                    exit;
                } 
            }

            if($filename === ''){
                $filename = '../public/img/common.png';
            }

            $this->userModel->store($body, $filename);
            echo json_encode(['status' => 200, 'message'=> 'Success Create User']);
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