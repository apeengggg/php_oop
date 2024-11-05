<?php

class Event {
    private $conn;
    private $table = "m_events";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function countAll(){
    }

    public function all($param) {
        $params = [];

        $query = "SELECT m_events.*, image FROM " . $this->table;

        $countQuery = "SELECT COUNT(*) as total FROM ". $this->table;

        $query .= ' WHERE 1=1 AND status = 1 ';
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

        if (!empty($param['date_start']) && !empty($param['date_end'])) {
            $query .= ' AND date BETWEEN :date_start AND :date_end ';
            $countQuery .= ' AND date BETWEEN :date_start AND :date_end ';
            $params[':date_start'] = $param['date_start'];
            $params[':date_end'] = $param['date_end'];
        }

        if(!empty($param['orderBy'])) {
            $query .= ' ORDER BY '. $param['orderBy'].' '.$param['dir'];
        }

        $page = isset($param['page']) && is_numeric($param['page']) ? (int)$param['page'] : 1; // Default to page 1
        $limit = isset($param['perPage']) && is_numeric($param['perPage']) ? (int)$param['perPage'] : 10; // Default limit is 10
        $offset = ($page - 1) * $limit;

        
        $query .= ' LIMIT :limit OFFSET :offset ';
        // var_dump($query);

        $stmt = $this->conn->prepare($query);
        $stmtCount = $this->conn->prepare($countQuery);

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
            $stmtCount->bindValue($key, $value);
        }

        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();
        $stmtCount->execute();

        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $totalRows = $stmtCount->fetch(PDO::FETCH_ASSOC)['total'];
        $totalPages = ceil($totalRows / $param['perPage']);

        return ['data' => $data, 'totalPages' => $totalPages];
    }

    public function store($body, $filename){
        try{
            $event_id = uniqid();
            $query = "INSERT INTO ".$this->table. " (event_id, event_name, location, date, start_time, description, image, total_ticket, available_ticket) VALUES (:event_id, :event_name, :location, :date, :time, :description, :image, :total_ticket, :available_ticket)";
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
        }catch(\Exception $e){
            echo json_encode(['status' => 500, 'message' => 'Internal Server Error', 'error' => $e->getMessage()]);
            exit;
        }
    }

    public function update($body, $filename){
        try{
            $query = "UPDATE ". $this->table . " SET event_name = :event_name, location = :location, date = :date, start_time = :start_time, description = :description, image = :image, total_ticket = :total_ticket, available_ticket = :available_ticket WHERE event_id = :event_id";
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':event_id', $body['event_id']);
            $stmt->bindParam(':event_name', $body['eventName']);
            $stmt->bindParam(':location', $body['eventLocation']);
            $stmt->bindParam(':date', $body['eventDate']);
            $stmt->bindParam(':start_time', $body['eventTime']);
            $stmt->bindParam(':description', $body['eventDescription']);
            $stmt->bindParam(':total_ticket', $body['availableTicket']);
            $stmt->bindParam(':available_ticket', $body['newAvailableTicket']);
            $stmt->bindParam(':image', $filename);

            $stmt->execute();
        }catch(\Exception $e){
            echo json_encode(['status' => 500, 'message' => 'Internal Server Error', 'error' => $e->getMessage()]);
            exit;
        }
    }

    public function destroy($event_id){
        try{
            $query = "UPDATE " . $this->table . " SET status = 0 WHERE event_id = :event_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':event_id', $event_id);
            $stmt->execute();
            return true;
        }catch(\Exception $e){
            echo json_encode(['status' => 500, 'message' => 'Internal Server Error', 'error' => $e->getMessage()]);
            exit;
        }
    }


    public function findEventByEventId($event_id){
        try{
            $query = "SELECT event_id, image, available_ticket, total_ticket FROM " . $this->table . " WHERE event_id = :event_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':event_id', $event_id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }catch(\Exception $e){
            echo json_encode(['status' => 500, 'message' => 'Internal Server Error', 'error' => $e->getMessage()]);
            exit;
        }
    }

    public function findUserByUsername($username, $user_id){
        try{
            $query = "SELECT username FROM " . $this->table . " WHERE username = :username AND user_id <> :user_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }catch(\Exception $e){
            echo json_encode(['status' => 500, 'message' => 'Internal Server Error', 'error' => $e->getMessage()]);
            exit;
        }
    }
}