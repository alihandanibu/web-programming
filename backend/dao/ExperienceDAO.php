<?php
require_once 'BaseDAO.php';

class ExperienceDAO extends BaseDAO {
    public function __construct() {
        parent::__construct('experiences');
    }

    public function findByUserId($user_id) {
        $query = "SELECT * FROM " . $this->table . " WHERE user_id = :user_id ORDER BY start_date DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>