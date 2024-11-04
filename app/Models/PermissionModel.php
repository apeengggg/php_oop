<?php

class Permission {
    private $conn;
    private $table = "m_permissions";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function permissionByRole($user_id, $function_id) {
        $query = "SELECT m_users.user_id, m_permissions.* FROM " . $this->table . " JOIN m_roles ON m_permissions.role_id = m_roles.role_id JOIN m_users ON m_roles.role_id = m_users.role_id WHERE m_users.user_id = :user_id AND m_permissions.function_id = :function_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':function_id', $function_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}