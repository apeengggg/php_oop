<?php 

require_once __DIR__.'/../Models/TransactionModel.php';
require_once __DIR__.'/../Helpers/Validation.php';

class TransactionController {
    private $trModel;

    public function __construct($db)
    {
        $this->trModel = new Transaction($db);
    }

    public function index(){
        include __DIR__.'/../Views/Transaction/index.php';
    }

    public function get(){
        $param = $_GET;
        $user_id = null;
        try{
            if($_SESSION['user']['role_id'] === '2'){
                $user_id = $_SESSION['user']['user_id'];
            }

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

            $results = $this->trModel->all($param, $user_id);
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
                'eventName' => ['required'],
                'eventDate' => ['required'],
                'eventTime' => ['required'],
                'eventLocation' => ['required'],
                'eventDescription' => ['required'],
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

            $this->trModel->store($body, $filename);
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
                'event_id' => ['required'],
                'eventName' => ['required'],
                'eventDate' => ['required'],
                'eventTime' => ['required'],
                'eventLocation' => ['required'],
                'eventDescription' => ['required'],
            ];

            $helper = new Validation();
            $validate = $helper->validate($body, $rules);
            if(!empty($validate)){
                echo json_encode(['status' => 400, 'message' => $validate]);
                exit;
            }

            $event = $this->trModel->findEventByEventId($body['event_id']);

            if(empty($event)){
                echo json_encode(['status' => 400, 'message'=> 'Event Not Found']);
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

                $eventImage = preg_replace('/^\.\.\//', '', $event['image']);

                if(file_exists($eventImage)){
                    if(!unlink($eventImage)){
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
                $filename = $event['image'];
            }

            $this->trModel->update($body, $filename);
            echo json_encode(['status' => 200, 'message'=> 'Success Update Event']);
        }catch(\Exception $e){
            echo json_encode(['status' => 500, 'message' => 'Internal Server Error', 'error' => $e->getMessage()]);
        }
    }

    public function destroy(){
        try{
            $rules = [
                'event_booking_id' => ['required'],
            ];

            $helper = new Validation();
            $validate = $helper->validate($_POST, $rules);
            if(!empty($validate)){
                echo json_encode(['status' => 400, 'message' => $validate]);
                exit;
            }

            $delete = $this->trModel->destroy($_POST['event_booking_id']);
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