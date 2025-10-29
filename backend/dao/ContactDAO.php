<?php
require_once 'BaseDAO.php';

class ContactDAO extends BaseDAO {
    public function __construct() {
        parent::__construct('contacts');
    }

    public function findByStatus($status) {
        $query = "SELECT * FROM " . $this->table . " WHERE status = :status ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>