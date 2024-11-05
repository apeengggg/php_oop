<?php
class Database
{
    private $host = 'localhost';
    private $dbName = 'pmpland';
    private $username = 'root';
    private $password = '';
    private $connection;
    private $isTransactionActive = false;

    public function __construct()
    {
        try {
            $this->connection = new PDO(
                "mysql:host={$this->host};dbname={$this->dbName}",
                $this->username,
                $this->password
            );

            // echo 'Database Successfullt connect!';

            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }

    public function beginTransaction() {
        if (!$this->isTransactionActive) {
            $this->connection->beginTransaction();
            $this->isTransactionActive = true;
        }
    }

    public function commit() {
        if ($this->isTransactionActive) {
            $this->connection->commit();
            $this->isTransactionActive = false;
        }
    }

    public function rollback() {
        if ($this->isTransactionActive) {
            $this->connection->rollBack();
            $this->isTransactionActive = false;
        }
    }

    public function getConnection()
    {
        return $this->connection;
    }
}
