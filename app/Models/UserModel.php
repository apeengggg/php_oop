<?php

class User {
    private $conn;
    private $table = "m_users";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function all($param) {
        $params = [];

        $query = "SELECT name, username, user_id FROM " . $this->table . ' WHERE 1=1 ';

        if (!empty($param['username'])) {
            $query .= ' AND username = :username ';
            $params[':username'] = $param['username'];
        }
        
        if (!empty($param['name'])) {
            $query .= ' AND name = :name ';
            $params[':name'] = $param['name'];
        }

        if(!empty($param['orderBy'])) {
            $query .= ' ORDER BY '. $param['orderBy'].' '.$param['dir'];
        }

        $page = isset($param['page']) && is_numeric($param['page']) ? (int)$param['page'] : 1; // Default to page 1
        $limit = isset($param['perPage']) && is_numeric($param['perPage']) ? (int)$param['perPage'] : 10; // Default limit is 10
        $offset = ($page - 1) * $limit;

        $query .= ' LIMIT :limit OFFSET :offset ';

        $stmt = $this->conn->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findUserByEmail($username) {
        $query = "SELECT name, username, user_id FROM " . $this->table . " WHERE username = :username LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createUser($data) {
        $query = "INSERT INTO " . $this->table . " (email, password, role) VALUES (:email, :password, :role)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':password', password_hash($data['password'], PASSWORD_DEFAULT));
        $stmt->bindParam(':role', $data['role']);
        return $stmt->execute();
    }
}