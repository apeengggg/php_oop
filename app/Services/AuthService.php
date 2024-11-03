<?php
class AuthService{
    private $db;
    private $token;

    public function __construct(Database $database, Token $jwt)
    {
        $this->db = $database->getConnection();
        $this->token = $jwt;
    }

    public function login($username, $password){
        $stmt = $this->db->prepare("SELECT user_id, username, password, name FROM m_users WHERE username = :username LIMIT 1");

        $stmt->bindParam(':username', $username);
        
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result) {
            $hashedPassword = $result['password'];
            $valid = password_verify($password, $hashedPassword);
            if ($valid) {
                if (isset($result['password'])) {
                    unset($result['password']);
                }
                $jwt = $this->token->generateToken($result);
                $_SESSION['token'] = $jwt;
                echo json_encode(['status' => 200, 'message' => 'Login Successfully', 'data' => $result, 'token' => $jwt]);
            }else{
                echo json_encode(['status' => 400, 'message' => 'Login Failed']);
            }
        }else{
            echo json_encode(['status' => 400, 'message' => 'Login Failed']);
        }  
    }
}