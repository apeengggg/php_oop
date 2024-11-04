<?php

class Event {
    private $conn;
    private $table = "m_users";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function countAll(){
    }

    public function all($param) {
        $params = [];

        $query = "SELECT name, username, user_id, role_name, m_users.role_id, image FROM " . $this->table;

        $countQuery = "SELECT COUNT(*) as total FROM ". $this->table;
        
        $query .= ' INNER JOIN m_roles ON m_users.role_id = m_roles.role_id ';

        $query .= ' WHERE 1=1 AND status = 1 ';
        $countQuery .= ' WHERE 1=1 AND status = 1 ';

        if (!empty($param['username'])) {
            $query .= ' AND LOWER(username) LIKE LOWER(:username) ';
            $countQuery .= ' AND LOWER(username) LIKE LOWER(:username) ';
            $params[':username'] = '%' . $param['username'] . '%';
        }
        
        if (!empty($param['name'])) {
            $query .= ' AND LOWER(name) LIKE LOWER(:name) ';
            $countQuery .= ' AND LOWER(name) LIKE LOWER(:name) ';
            $params[':name'] = '%' . $param['name'] . '%';
        }

        if (!empty($param['role'])) {
            $query .= ' AND m_users.role_id = :role ';
            $countQuery .= ' AND m_users.role_id = :role ';
            $params[':role'] = $param['role'];
        }

        if(!empty($param['orderBy'])) {
            $query .= ' ORDER BY '. $param['orderBy'].' '.$param['dir'];
        }

        $page = isset($param['page']) && is_numeric($param['page']) ? (int)$param['page'] : 1; // Default to page 1
        $limit = isset($param['perPage']) && is_numeric($param['perPage']) ? (int)$param['perPage'] : 10; // Default limit is 10
        $offset = ($page - 1) * $limit;

        $query .= ' LIMIT :limit OFFSET :offset ';

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