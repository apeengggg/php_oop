<?php
require_once __DIR__.'/../Helpers/Response.php';

class Dashboard {
    private $conn;
    private $table_event = "m_events AS e";
    protected $response;

    public function __construct($db) {
        $this->conn = $db;
        $this->response = new Response();
    }

    public function countAll(){
    }

    public function dashboardUser($param, $seeAll = 0) {
        $params = [];

        $query = "SELECT e.*, m_categories.category_name FROM " . $this->table_event;
        $query .= " JOIN m_categories ON e.category_id = m_categories.category_id ";
        
        $countQuery = "SELECT COUNT(*) as total FROM ". $this->table_event;
        $countQuery .= " JOIN m_categories ON e.category_id = m_categories.category_id ";

        $query .= ' WHERE 1=1 AND e.status = 1';
        
        $countQuery .= ' WHERE 1=1 AND e.status = 1 ';

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

        if (!empty($param['category'])) {
            $query .= ' AND e.category_id = :category ';
            $countQuery .= ' AND e.category_id = :category ';

            $params[':category'] = $param['category'];
        }

        if (!empty($param['date_start']) && !empty($param['date_end'])) {
            $query .= ' AND date BETWEEN :date_start AND :date_end ';
            $countQuery .= ' AND date BETWEEN :date_start AND :date_end ';

            $params[':date_start'] = $param['date_start'];
            $params[':date_end'] = $param['date_end'];
        }else{
            $query .= ' AND date >= CURDATE()';
            $countQuery .= ' AND date >= CURDATE()';
        }

        if(!empty($param['orderBy'])) {
            $query .= ' ORDER BY '. $param['orderBy'].' '.$param['dir'];
            $countQuery .= ' ORDER BY '. $param['orderBy'].' '.$param['dir'];
        }else{
            $query .= ' ORDER BY date ASC';
        }


        $page = isset($param['page']) && is_numeric($param['page']) ? (int)$param['page'] : 1; // Default to page 1
        $limit = isset($param['perPage']) && is_numeric($param['perPage']) ? (int)$param['perPage'] : 6; // Default limit is 10
        $offset = ($page - 1) * $limit;

        $query .= ' LIMIT :limit OFFSET :offset ';

        // var_dump($query);
        // exit;

        $stmt = $this->conn->prepare($query);
        if($seeAll === 1){
            $stmtCount = $this->conn->prepare($countQuery);
        }

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
            if($seeAll === 1){
                $stmtCount->bindValue($key, $value);
            }
        }

        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();
        if($seeAll === 1){
            $stmtCount->execute();
        }

        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if($seeAll === 1){
            $totalRows = $stmtCount->fetch(PDO::FETCH_ASSOC)['total'];
            $totalPages = ceil($totalRows / $param['perPage']);
        }

        $data = ['data' => $data];
        if($seeAll === 1){
            $data['totalPages'] = $totalPages;
        }

        return $data;
    }

    public function dashboardAdmin() {
        $query_event = "SELECT e.event_name, e.total_ticket, ROUND((COUNT(b.event_booking_id) / e.total_ticket) * 100, 2) AS ticket_sold_percent, COUNT(b.event_booking_id) AS ticket_sold FROM " . $this->table_event ;
        $query_event .= " LEFT JOIN r_event_booking AS b ON e.event_id = b.event_id AND (b.status = 1 OR b.status = 3) ";
        $query_event .= ' WHERE 1=1 AND e.status = 1';
        $query_event .= ' GROUP BY e.event_name';
        $query_event .= ' ORDER BY e.date ASC';

        $stmt = $this->conn->prepare($query_event);
        $stmt->execute();
        $data_event = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $data = ['data_event' => $data_event];

        return $data;
    }
}