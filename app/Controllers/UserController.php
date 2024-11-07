<?php 

require_once __DIR__.'/../Models/UserModel.php';
require_once __DIR__.'/../Helpers/Response.php';
require_once __DIR__.'/../Helpers/Validation.php';

class UserController {
    private $userModel;
    protected $response;

    public function __construct($db)
    {
        $this->userModel = new User($db);
        $this->response = new Response();
    }

    public function index(){
        include __DIR__.'/../Views/User/index.php';
    }

    public function get(){
        $param = $_GET;
        try{

            $rules = [
                'page' => ['required', 'numeric'],
                'perPage' => ['required', 'numeric'],
                'orderBy' => ['required'],
                'dir' => ['required', 'equal:asc|desc'],
            ];

            $helper = new Validation();
            $validate = $helper->validate($param, $rules);
            if(!empty($validate)){
                echo $this->response->BadRequest($validate);
                exit;
            }

            $results = $this->userModel->all($param);
            $totalPages = $results['totalPages'];
            $data = $results['data'];

            header('Content-Type: application/json');
            echo $this->response->OkPaging($data, "Success Get Data", $totalPages);
        }catch(\Exception $e){
            echo $this->response->InternalServerError($e->getMessage());

        }
    }

    public function store(){
        header('Content-Type: application/json');

        $body = $_POST;
        $filename = '';
        try{
            $rules = [
                'username' => ['required'],
                'name' => ['required'],
                'password' => ['required'],
                'role' => ['required'],
            ];

            $helper = new Validation();
            $validate = $helper->validate($body, $rules);
            if(!empty($validate)){
                echo $this->response->BadRequest($validate);
                exit;
            }

            if(!empty($_FILES) || isset($_FILES['image'])){
                $file = $_FILES['image'];

                if ($file['error'] !== UPLOAD_ERR_OK) {
                    echo $this->response->BadRequest("Error Uploading Image");
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
                    echo $this->response->BadRequest("Error Uploading Image");
                    exit;
                } 
            }

            if($filename === ''){
                $filename = '../public/img/common.png';
            }

            $this->userModel->store($body, $filename);
            echo $this->response->Success("Success Create User");
        }catch(\Exception $e){
            echo $this->response->InternalServerError($e->getMessage());
        }
    }

    public function update(){
        header('Content-Type: application/json');

        $body = $_POST;
        $filename = '';
        try{
            $rules = [
                'user_id' => ['required'],
                'username' => ['required'],
                'name' => ['required'],
                'role' => ['required'],
            ];

            $helper = new Validation();
            $validate = $helper->validate($body, $rules);
            if(!empty($validate)){
                echo $this->response->BadRequest($validate);
                exit;
            }

            $user = $this->userModel->findUserByUserId($body['user_id']);

            if(empty($user)){
                echo $this->response->BadRequest("User Not Found");
                exit;
            }

            $username_unique = $this->userModel->findUserByUsername($body['username'], $body['user_id']);
            if($username_unique){
                echo $this->response->BadRequest("Username already exists!");
                exit;
            }

            if(!empty($_FILES) || isset($_FILES['image'])){
                $file = $_FILES['image'];

                if ($file['error'] !== UPLOAD_ERR_OK) {
                    echo $this->response->BadRequest("Error Uploading Image");
                    exit;
                }

                $uploadDir = 'public/img/profile/';
                $uniqueName = uniqid() . '_' . basename($file['name']);
                $uploadFile = $uploadDir . $uniqueName;

                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $userImage = preg_replace('/^\.\.\//', '', $user['image']);

                if(file_exists($userImage)){
                    if($userImage != 'public/img/common.png'){
                        if(!unlink($userImage)){
                            echo $this->response->BadRequest("Failed Change Image");
                            exit;
                        }
                    }

                    if (move_uploaded_file($file['tmp_name'], $uploadFile)) {
                        $filename = '../'.$uploadDir.$uniqueName;
                    } else {
                        echo $this->response->BadRequest("Error Uploading Image");
                        exit;
                    }
                }
            }

            if($filename === ''){
                $filename = $user['image'];
            }

            $this->userModel->update($body, $filename);
            echo $this->response->Success("Success Update User");
        }catch(\Exception $e){
            echo $this->response->InternalServerError($e->getMessage());
        }
    }

    public function destroy(){
        try{
            $rules = [
                'user_id' => ['required'],
            ];

            $helper = new Validation();
            $validate = $helper->validate($_POST, $rules);
            if(!empty($validate)){
                echo $this->response->BadRequest($validate);
                exit;
            }

            $this->userModel->destroy($_POST['user_id']);
            echo $this->response->Success("Success Delete Data");
        }catch(\Exception $e){
            echo $this->response->InternalServerError($e->getMessage());
        }
    }


}