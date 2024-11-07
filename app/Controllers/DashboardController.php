<?php 

require_once __DIR__.'/../Models/DashboardModel.php';
require_once __DIR__.'/../Helpers/Response.php';
require_once __DIR__.'/../Helpers/Validation.php';

class DashboardController {
    private $dsModel;
    protected $response;

    public function __construct($db)
    {
        $this->dsModel = new Dashboard($db);
        $this->response = new Response();
    }

    public function index(){
        if($_SESSION['user']['role_id'] == '2'){
            include __DIR__.'/../Views/Dashboard/user.php';
        }else{
            include __DIR__.'/../Views/Dashboard/admin.php';
        }
    }

    public function getUser(){
        $param = $_GET;
        try{
            
            $seeAll = 0;
            if(isset($param['page'])){
                $seeAll = 1;
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
            }

            $data = $this->dsModel->dashboardUser($param, $seeAll);
            header('Content-Type: application/json');
            if(isset($data['totalPages'])){
                echo $this->response->OkPaging($data['data'], "Success Get Data", $data['totalPages']);
            }else{
                echo $this->response->Ok($data['data'], "Success Get Data");
            }
        }catch(\Exception $e){
            echo $this->response->InternalServerError($e->getMessage());
        }
    }
}