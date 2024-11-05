<?php

require_once __DIR__.'/../Helpers/Response.php';

class Transaction {
    private $conn;
    private $table = "r_event_booking";
    private $response;

    public function __construct($db) {
        $this->conn = $db;
        $this->response = new Response();
    }

    public function all($param, $user_id = null) {
        $params = [];

        $query = "SELECT r_event_booking.event_booking_id, r_event_booking.status AS status_ticket, m_events.*, m_users.username FROM " . $this->table;

        $countQuery = "SELECT COUNT(*) as total FROM ". $this->table;

        $query .= " JOIN m_events ON r_event_booking.event_id = m_events.event_id ";

        $query .= " JOIN m_users ON r_event_booking.user_id = m_users.user_id ";

        $countQuery .= " JOIN m_events ON r_event_booking.event_id = m_events.event_id ";

        $countQuery .= " JOIN m_users ON r_event_booking.user_id = m_users.user_id ";

        $query .= ' WHERE 1=1 ';

        $countQuery .= ' WHERE 1=1 ';


        if (!empty($param['event_name'])) {
            $query .= ' AND LOWER(m_events.event_name) LIKE LOWER(:event_name) ';
            $countQuery .= ' AND LOWER(m_events.event_name) LIKE LOWER(:event_name) ';
            $params[':event_name'] = '%' . $param['event_name'] . '%';
        }

        if ($user_id != null || isset($param['user_id'])) {
            $query .= 'AND m_users.user_id = :user_id ';
            $countQuery .= 'AND m_users.user_id = :user_id ';
            $params[':user_id'] = $user_id != null ? $user_id : $param['user_id'];
        }

        // var_dump($query);

        if (!empty($param['username'])) {
            $query .= ' AND LOWER(username) LIKE LOWER(:username) ';
            $countQuery .= ' AND LOWER(username) LIKE LOWER(:username) ';
            $params[':username'] = '%' . $param['username'] . '%';
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

        // var_dump($query);

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
            $query = "INSERT INTO ".$this->table. " (event_id, event_name, location, date, start_time, description, image) VALUES (:event_id, :event_name, :location, :date, :time, :description, :image)";
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':event_id', $event_id);
            $stmt->bindParam(':event_name', $body['eventName']);
            $stmt->bindParam(':location', $body['eventLocation']);
            $stmt->bindParam(':date', $body['eventDate']);
            $stmt->bindParam(':time', $body['eventTime']);
            $stmt->bindParam(':description', $body['eventDescription']);
            $stmt->bindParam(':image', $filename);

            $stmt->execute();
            return true;
        }catch(PDOException $e){
            echo json_encode(['status' => 500, 'message' => 'Internal Server Error', 'error' => $e->getMessage(), ]);
            exit;
        }catch(\Exception $e){
            echo $this->response->InternalServerError($e->getMessage());
            exit;
        }
    }

    public function update($body, $filename){
        try{
            $query = "UPDATE ". $this->table . " SET event_name = :event_name, location = :location, date = :date, start_time = :start_time, description = :description, image = :image WHERE event_id = :event_id";
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':event_id', $body['event_id']);
            $stmt->bindParam(':event_name', $body['eventName']);
            $stmt->bindParam(':location', $body['eventLocation']);
            $stmt->bindParam(':date', $body['eventDate']);
            $stmt->bindParam(':start_time', $body['eventTime']);
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

    public function destroy($event_booking_id){
        try{
            $this->conn->beginTransaction();

            $query = "UPDATE " . $this->table . " SET status = 0 WHERE event_booking_id = :event_booking_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':event_booking_id', $event_booking_id);
            $stmt->execute();

            $select_event = "SELECT event_id FROM r_event_booking WHERE event_booking_id = :event_booking_id";
            $stmt_event = $this->conn->prepare($select_event);
            $stmt_event->bindParam(':event_booking_id', $event_booking_id);
            $stmt_event->execute();
            $data_event = $stmt_event->fetch(PDO::FETCH_ASSOC);

            $queryUpdateTicket = "UPDATE m_events SET available_ticket = CASE WHEN available_ticket < total_ticket THEN available_ticket + 1 ELSE available_ticket END WHERE event_id = :event_id;";
            $stmt_update = $this->conn->prepare($queryUpdateTicket);
            $stmt_update->bindParam(':event_id', $data_event['event_id']); 
            $stmt_update->execute();
        }catch(PDOException $e){
            echo $this->response->InternalServerError($e->getMessage());
            exit;
        }catch(\Exception $e){
            echo $this->response->InternalServerError($e->getMessage());
            exit;
        }
    }


    public function findEventByEventId($event_id){
        try{
            $query = "SELECT event_id, image FROM " . $this->table . " WHERE event_id = :event_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':event_id', $event_id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }catch(PDOException $e){
            echo $this->response->InternalServerError($e->getMessage());
            exit;
        }catch(\Exception $e){
            echo $this->response->InternalServerError($e->getMessage());
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
        }catch(PDOException $e){
            echo $this->response->InternalServerError($e->getMessage());
            exit;
        }catch(\Exception $e){
            echo $this->response->InternalServerError($e->getMessage());
            exit;
        }
    }
}