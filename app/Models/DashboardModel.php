<?php
require_once __DIR__.'/../Helpers/Response.php';

class Dashboard {
    private $conn;
    private $table_event = "m_events";
    protected $response;

    public function __construct($db) {
        $this->conn = $db;
        $this->response = new Response();
    }

    public function countAll(){
    }

    public function dashboardUser($param, $seeAll = 0) {
        $params = [];

        $query = "SELECT m_events.*, m_categories.category_name FROM " . $this->table_event;
        $query .= " JOIN m_categories ON m_events.category_id = m_categories.category_id ";
        
        $countQuery = "SELECT COUNT(*) as total FROM ". $this->table_event;
        $countQuery .= " JOIN m_categories ON m_events.category_id = m_categories.category_id ";

        $query .= ' WHERE 1=1 AND status = 1';
        
        $countQuery .= ' WHERE 1=1 AND status = 1 ';

        if (!empty($param['event_name'])) {
            $query .= ' AND LOWER(event_name) LIKE LOWER(:event_name) ';
            $countQuery .= ' AND LOWER(event_name) LIKE LOWER(:event_name) ';
            $params[':event_name'] = '%' . $param['event_name'] . '%';
        }

        if (!empty($param['location'])) {
            $query .= ' AND LOWER(location) LIKE LOWER(:location) ';
            $countQuery .= ' AND LOWER(location) LIKE LOWER(:location) ';
            $params[':location'] = '%' . $param['location'] . '%';
        }

        if (!empty($param['category'])) {
            $query .= ' AND m_events.category_id = :category ';
            $countQuery .= ' AND m_events.category_id = :category ';

            $params[':category'] = $param['category'];
        }

        if (!empty($param['date_start']) && !empty($param['date_end'])) {
            $query .= ' AND date BETWEEN :date_start AND :date_end ';
            $countQuery .= ' AND date BETWEEN :date_start AND :date_end ';

            $params[':date_start'] = $param['date_start'];
            $params[':date_end'] = $param['date_end'];
        }else{
            $query .= ' AND date >= CURDATE()';
            $countQuery .= ' AND date >= CURDATE()';
        }

        if(!empty($param['orderBy'])) {
            $query .= ' ORDER BY '. $param['orderBy'].' '.$param['dir'];
            $countQuery .= ' ORDER BY '. $param['orderBy'].' '.$param['dir'];
        }else{
            $query .= ' ORDER BY date ASC';
        }


        $page = isset($param['page']) && is_numeric($param['page']) ? (int)$param['page'] : 1; // Default to page 1
        $limit = isset($param['perPage']) && is_numeric($param['perPage']) ? (int)$param['perPage'] : 6; // Default limit is 10
        $offset = ($page - 1) * $limit;

        $query .= ' LIMIT :limit OFFSET :offset ';

        // var_dump($query);
        // exit;

        $stmt = $this->conn->prepare($query);
        if($seeAll === 1){
            $stmtCount = $this->conn->prepare($countQuery);
        }

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
            if($seeAll === 1){
                $stmtCount->bindValue($key, $value);
            }
        }

        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();
        if($seeAll === 1){
            $stmtCount->execute();
        }

        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if($seeAll === 1){
            $totalRows = $stmtCount->fetch(PDO::FETCH_ASSOC)['total'];
            $totalPages = ceil($totalRows / $param['perPage']);
        }

        $data = ['data' => $data];
        if($seeAll === 1){
            $data['totalPages'] = $totalPages;
        }

        return $data;
    }

    public function store($body, $filename){
        try{
            $event_id = uniqid();
            $query = "INSERT INTO ".$this->table_event. " (event_id, event_name, location, date, start_time, description, image, total_ticket, available_ticket) VALUES (:event_id, :event_name, :location, :date, :time, :description, :image, :total_ticket, :available_ticket)";
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':event_id', $event_id);
            $stmt->bindParam(':event_name', $body['eventName']);
            $stmt->bindParam(':location', $body['eventLocation']);
            $stmt->bindParam(':date', $body['eventDate']);
            $stmt->bindParam(':time', $body['eventTime']);
            $stmt->bindParam(':total_ticket', $body['availableTicket']);
            $stmt->bindParam(':available_ticket', $body['availableTicket']);
            $stmt->bindParam(':description', $body['eventDescription']);
            $stmt->bindParam(':image', $filename);

            $stmt->execute();
            return true;
        }catch(PDOException $e){
            echo $this->response->InternalServerError($e->getMessage());
            exit;
        }catch(\Exception $e){
            echo $this->response->InternalServerError($e->getMessage());
            exit;
        }
    }
}