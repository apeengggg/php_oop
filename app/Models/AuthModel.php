<?php
class Auth{
    private $db;

    public function __construct(Database $database)
    {
        $this->db = $database->getConnection();
    }

    public function login($username){
        $stmt = $this->db->prepare("SELECT user_id, username, password, name, m_users.role_id, role_name FROM m_users JOIN m_roles ON m_users.role_id = m_roles.role_id WHERE username = :username LIMIT 1");

        $stmt->bindParam(':username', $username);
        
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC); 
    }

    public function getPermission($role_id){
        $stmt = $this->db->prepare("SELECT m_functions.function_name, m_functions.url, m_functions.icon, m_roles.role_name, m_permissions.* FROM m_permissions JOIN m_roles ON m_permissions.role_id = m_roles.role_id JOIN m_functions on m_permissions.function_id = m_functions.function_id WHERE m_permissions.role_id = :role_id");

        $stmt->bindParam(':role_id', $role_id);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}