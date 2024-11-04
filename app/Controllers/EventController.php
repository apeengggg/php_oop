<?php 

require_once __DIR__.'/../Models/EventModel.php';
require_once __DIR__.'/../Helpers/Validation.php';

class EventController {
    private $eventModel;

    public function __construct($db)
    {
        $this->eventModel = new Event($db);
    }

    public function index(){
        include __DIR__.'/../Views/Event/index.php';
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
                echo json_encode(['status' => 400, 'message' => $validate]);
                exit;
            }

            $results = $this->eventModel->all($param);
            $totalPages = $results['totalPages'];
            $data = $results['data'];

            header('Content-Type: application/json');
            echo json_encode(['status' => 200, 'data'=> $data, 'totalPages'=> $totalPages, 'message'=> 'Success Get Data']);
        }catch(\Exception $e){
            echo json_encode(['status' => 500, 'message' => 'Internal Server Error', 'error' => $e->getMessage()]);

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
                echo json_encode(['status' => 400, 'message' => $validate]);
                exit;
            }

            if(!empty($_FILES) || isset($_FILES['image'])){
                $file = $_FILES['image'];

                if ($file['error'] !== UPLOAD_ERR_OK) {
                    echo json_encode(['status' => 400, 'message'=> 'Error Uploading Image']);
                    exit;
                }

                $uploadDir = 'public/img/events/';
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
                $filename = '../public/img/common_event.png';
            }

            $this->eventModel->store($body, $filename);
            echo json_encode(['status' => 200, 'message'=> 'Success Create User']);
        }catch(\Exception $e){
            echo json_encode(['status' => 500, 'message' => 'Internal Server Error', 'error' => $e->getMessage()]);
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
                echo json_encode(['status' => 400, 'message' => $validate]);
                exit;
            }

            $user = $this->eventModel->findUserByUserId($body['user_id']);

            if(empty($user)){
                echo json_encode(['status' => 400, 'message'=> 'User Not Found']);
                exit;
            }

            $username_unique = $this->eventModel->findUserByUsername($body['username'], $body['user_id']);
            if($username_unique){
                echo json_encode(['status' => 400, 'message'=> 'Username already exists!']);
                exit;
            }

            if(!empty($_FILES) || isset($_FILES['image'])){
                $file = $_FILES['image'];

                if ($file['error'] !== UPLOAD_ERR_OK) {
                    echo json_encode(['status' => 400, 'message'=> 'Error Uploading Image']);
                    exit;
                }

                $uploadDir = 'public/img/events/';
                $uniqueName = uniqid() . '_' . basename($file['name']);
                $uploadFile = $uploadDir . $uniqueName;

                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $userImage = preg_replace('/^\.\.\//', '', $user['image']);

                if(file_exists($userImage)){
                    if(!unlink($userImage)){
                        echo json_encode(['status' => 400, 'message'=> 'Failed Change Image']);
                        exit;
                    }

                    if (move_uploaded_file($file['tmp_name'], $uploadFile)) {
                        $filename = '../'.$uploadDir.$uniqueName;
                    } else {
                        echo json_encode(['status' => 400, 'message'=> 'Error Uploading Image']);
                        exit;
                    }
                }
            }

            if($filename === ''){
                $filename = $user['image'];
            }

            $this->eventModel->update($body, $filename);
            echo json_encode(['status' => 200, 'message'=> 'Success Update User']);
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
            $rules = [
                'user_id' => ['required'],
            ];

            $helper = new Validation();
            $validate = $helper->validate($_POST, $rules);
            if(!empty($validate)){
                echo json_encode(['status' => 400, 'message' => $validate]);
                exit;
            }

            $delete = $this->eventModel->destroy($_POST['user_id']);
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