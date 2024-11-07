<?php 

require_once __DIR__.'/../Models/EventModel.php';
require_once __DIR__.'/../Helpers/Response.php';
require_once __DIR__.'/../Helpers/Validation.php';

class EventController {
    private $eventModel;
    protected $response;

    public function __construct($db)
    {
        $this->eventModel = new Event($db);
        $this->response = new Response();
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
                echo $this->response->BadRequest($validate);
                exit;
            }

            $results = $this->eventModel->all($param);
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
                'eventName' => ['required'],
                'eventDate' => ['required'],
                'eventTime' => ['required'],
                'eventLocation' => ['required'],
                'eventDescription' => ['required'],
                'availableTicket' => ['required', 'numeric'],
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

                $uploadDir = 'public/img/events/';
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
                $filename = '../public/img/common_event.png';
            }

            $this->eventModel->store($body, $filename);
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
                'event_id' => ['required'],
                'eventName' => ['required'],
                'eventDate' => ['required'],
                'eventTime' => ['required'],
                'eventLocation' => ['required'],
                'eventDescription' => ['required'],
                'availableTicket' => ['required', 'numeric'],
            ];

            $helper = new Validation();
            $validate = $helper->validate($body, $rules);
            if(!empty($validate)){
                echo $this->response->BadRequest($validate);
                exit;
            }

            $event = $this->eventModel->findEventByEventId($body['event_id']);

            if(empty($event)){
                echo $this->response->BadRequest("Event Not Found");
                exit;
            }

            if($body['availableTicket'] < $event['available_ticket']){
                echo $this->response->BadRequest("Total Ticket Must Be Greater Than Available Ticket");
                exit;
            }

            $sold = $event['total_ticket'] - $event['available_ticket'];
            $body['newAvailableTicket'] = $event['available_ticket'];
            if($body['availableTicket'] != $event['total_ticket']){
                $body['newAvailableTicket'] = $body['availableTicket'] - $sold;
            }

            if(!empty($_FILES) || isset($_FILES['image'])){
                $file = $_FILES['image'];

                if ($file['error'] !== UPLOAD_ERR_OK) {
                    echo $this->response->BadRequest("Error Uploading Image");
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
                        echo $this->response->BadRequest("Failed Change Image");
                        exit;
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
                $filename = $event['image'];
            }

            $this->eventModel->update($body, $filename);
            echo $this->response->Success("Success Update Event");
        }catch(\Exception $e){
            echo $this->response->InternalServerError($e->getMessage());
        }
    }

    public function destroy(){
        try{
            $rules = [
                'event_id' => ['required'],
            ];

            $helper = new Validation();
            $validate = $helper->validate($_POST, $rules);
            if(!empty($validate)){
                echo $this->response->BadRequest($validate);
                exit;
            }

            $delete = $this->eventModel->destroy($_POST['event_id']);
            echo $this->response->Success("Success Delete Data");
        }catch(\Exception $e){
            echo $this->response->InternalServerError($e->getMessage());
        }
    }


}