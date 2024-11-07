<?php
require_once __DIR__.'/../Helpers/Response.php';

class Event {
    private $conn;
    private $table = "m_events";
    protected $response;

    public function __construct($db) {
        $this->conn = $db;
        $this->response = new Response();
    }

    public function countAll(){
    }

    public function all($param) {
        $params = [];

        $query = "SELECT m_events.*, image, m_categories.category_name FROM " . $this->table;

        $query .= " JOIN m_categories ON m_events.category_id = m_categories.category_id ";
        
        $countQuery = "SELECT COUNT(*) as total FROM ". $this->table;

        $countQuery .= " JOIN m_categories ON m_events.category_id = m_categories.category_id ";

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

        if (!empty($param['category'])) {
            $query .= ' AND m_events.category_id = :category ';
            $countQuery .= ' AND m_events.category_id = :category ';
            $params[':category'] = $param['category'];
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
            $query = "INSERT INTO ".$this->table. " (event_id, event_name, location, date, start_time, description, image, total_ticket, available_ticket, category_id) VALUES (:event_id, :event_name, :location, :date, :time, :description, :image, :total_ticket, :available_ticket, :category)";
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':event_id', $event_id);
            $stmt->bindParam(':event_name', $body['eventName']);
            $stmt->bindParam(':location', $body['eventLocation']);
            $stmt->bindParam(':date', $body['eventDate']);
            $stmt->bindParam(':time', $body['eventTime']);
            $stmt->bindParam(':total_ticket', $body['availableTicket']);
            $stmt->bindParam(':available_ticket', $body['availableTicket']);
            $stmt->bindParam(':description', $body['eventDescription']);
            $stmt->bindParam(':category', $body['eventCategory']);
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

    public function update($body, $filename){
        try{
            $query = "UPDATE ". $this->table . " SET event_name = :event_name, location = :location, date = :date, start_time = :start_time, description = :description, image = :image, total_ticket = :total_ticket, available_ticket = :available_ticket, category_id = :category WHERE event_id = :event_id";
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':event_id', $body['event_id']);
            $stmt->bindParam(':event_name', $body['eventName']);
            $stmt->bindParam(':location', $body['eventLocation']);
            $stmt->bindParam(':date', $body['eventDate']);
            $stmt->bindParam(':start_time', $body['eventTime']);
            $stmt->bindParam(':description', $body['eventDescription']);
            $stmt->bindParam(':total_ticket', $body['availableTicket']);
            $stmt->bindParam(':category', $body['eventCategory']);
            $stmt->bindParam(':available_ticket', $body['newAvailableTicket']);
            $stmt->bindParam(':image', $filename);

            $stmt->execute();
        }catch(PDOException $e){
            echo $this->response->InternalServerError($e->getMessage());
            exit;
        }catch(\Exception $e){
            echo $this->response->InternalServerError($e->getMessage());
            exit;
        }
    }

    public function destroy($event_id){
        try{
            $this->conn->beginTransaction();

            $query = "UPDATE " . $this->table . " SET status = 0 WHERE event_id = :event_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':event_id', $event_id);

            $queryTr = "UPDATE r_event_booking SET status = 2 WHERE event_id = :event_id";
            $stmtTr = $this->conn->prepare($queryTr);
            $stmtTr->bindParam(':event_id', $event_id);

            $stmt->execute();
            $stmtTr->execute();

            $this->conn->commit();
        }catch(PDOException $e){
            $this->conn->rollback();
            echo $this->response->InternalServerError($e->getMessage());
            exit;
        }catch(\Exception $e){
            echo $this->response->InternalServerError($e->getMessage());
            exit;
        }
    }

    public function booking($event_id){
        try{
            $this->conn->beginTransaction();

            $eventBookingId = uniqid();
            $user_id = $_SESSION['user']['user_id'];

            $insert = "INSERT INTO r_event_booking (event_booking_id, event_id, user_id) VALUES (:event_booking_id, :event_id, :user_id)";
            $stmt = $this->conn->prepare($insert);
            $stmt->bindParam(':event_booking_id', $eventBookingId);
            $stmt->bindParam(':event_id', $event_id);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();

            $availableTicket = "UPDATE m_events SET available_ticket = available_ticket - 1 WHERE event_id = :event_id";
            $stmt = $this->conn->prepare($availableTicket);
            $stmt->bindParam(':event_id', $event_id);
            $stmt->execute();

            $this->conn->commit();
        }catch(PDOException $e){
            $this->conn->rollback();
            echo $this->response->InternalServerError($e->getMessage());
            exit;
        }catch(\Exception $e){
            echo $this->response->InternalServerError($e->getMessage());
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