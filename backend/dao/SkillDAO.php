<?php
require_once 'BaseDAO.php';

class SkillDAO extends BaseDAO {
    public function __construct() {
        parent::__construct('skills');
    }

    public function findByUserId($user_id) {
        $query = "SELECT * FROM " . $this->table . " WHERE user_id = :user_id ORDER BY proficiency DESC, name ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>