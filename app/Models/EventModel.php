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
            $user_id = uniqid();
            $query = "INSERT INTO m_users (user_id, name, username, password, role_id, image) VALUES (:user_id, :name, :username, :pass, :role_id, :image)";
            $stmt = $this->conn->prepare($query);

            $hashedPassword = password_hash($body['password'], PASSWORD_BCRYPT);

            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':name', $body['name']);
            $stmt->bindParam(':username', $body['username']);
            $stmt->bindParam(':pass', $hashedPassword);
            $stmt->bindParam(':image', $filename);
            $stmt->bindParam(':role_id', $body['role']);

            $stmt->execute();
            return true;
        }catch(\Exception $e){
            echo json_encode(['status' => 500, 'message' => 'Internal Server Error', 'error' => $e->getMessage()]);
            exit;
        }
    }

    public function update($body, $filename){
        try{
            $query = "UPDATE ". $this->table . " SET name = :name, username = :username, role_id = :role_id, image = :image WHERE user_id = :user_id";
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':user_id', $body['user_id']);
            $stmt->bindParam(':name', $body['name']);
            $stmt->bindParam(':username', $body['username']);
            $stmt->bindParam(':image', $filename);
            $stmt->bindParam(':role_id', $body['role']);

            $stmt->execute();
            return true;
        }catch(\Exception $e){
            echo json_encode(['status' => 500, 'message' => 'Internal Server Error', 'error' => $e->getMessage()]);
            exit;
        }
    }

    public function destroy($user_id){
        try{
            $query = "UPDATE " . $this->table . " SET status = 0 WHERE user_id = :user_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
            return true;
        }catch(\Exception $e){
            echo json_encode(['status' => 500, 'message' => 'Internal Server Error', 'error' => $e->getMessage()]);
            exit;
        }
    }

    public function findUserByUserId($user_id){
        try{
            $query = "SELECT user_id, username, image FROM " . $this->table . " WHERE user_id = :user_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $user_id);
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