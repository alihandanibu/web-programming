<?php
namespace app\services;

require_once __DIR__ . '/../dao/SkillDAO.php';

class SkillService {
    private $skillDAO;

    public function __construct() {
        $this->skillDAO = new \SkillDAO();
    }

    public function addSkill($userId, $data) {
        $proficiency = $data['proficiency'] ?? ($data['level'] ?? null);
        if (empty($data['name']) || empty($proficiency)) {
            return ['success' => false, 'message' => 'Skill name and proficiency are required'];
        }

        $proficiency = strtolower((string)$proficiency);
        $allowed = ['beginner', 'intermediate', 'advanced', 'expert'];
        if (!in_array($proficiency, $allowed, true)) {
            return ['success' => false, 'message' => 'Invalid proficiency'];
        }

        $skillData = [
            'user_id' => $userId,
            'name' => $data['name'],
            'proficiency' => $proficiency,
            'category' => $data['category'] ?? 'general'
        ];

        $skillId = $this->skillDAO->create($skillData);
        
        if ($skillId) {
            return ['success' => true, 'message' => 'Skill added successfully', 'skill_id' => $skillId];
        }

        return ['success' => false, 'message' => 'Failed to add skill'];
    }

    public function getSkillsByUser($userId) {
        $skills = $this->skillDAO->findByUserId($userId);
        
        if (empty($skills)) {
            return ['success' => true, 'skills' => []];
        }

        return ['success' => true, 'skills' => $skills];
    }

    public function updateSkill($userId, $skillId, $data) {
        $skill = $this->skillDAO->findById($skillId);
        
        if (!$skill || $skill['user_id'] != $userId) {
            return ['success' => false, 'message' => 'Skill not found'];
        }

        if (isset($data['level']) && !isset($data['proficiency'])) {
            $data['proficiency'] = $data['level'];
            unset($data['level']);
        }

        if (isset($data['proficiency'])) {
            $data['proficiency'] = strtolower((string)$data['proficiency']);
            $allowed = ['beginner', 'intermediate', 'advanced', 'expert'];
            if (!in_array($data['proficiency'], $allowed, true)) {
                return ['success' => false, 'message' => 'Invalid proficiency'];
            }
        }

        $allowedFields = ['name', 'proficiency', 'category'];
        $updateData = array_intersect_key($data, array_flip($allowedFields));
        if (empty($updateData)) {
            return ['success' => false, 'message' => 'No valid fields to update'];
        }

        $success = $this->skillDAO->update($skillId, $updateData);
        
        if ($success) {
            return ['success' => true, 'message' => 'Skill updated successfully'];
        }

        return ['success' => false, 'message' => 'Update failed'];
    }

    public function deleteSkill($userId, $skillId) {
        $skill = $this->skillDAO->findById($skillId);
        
        if (!$skill || $skill['user_id'] != $userId) {
            return ['success' => false, 'message' => 'Skill not found'];
        }

        $success = $this->skillDAO->delete($skillId);
        
        if ($success) {
            return ['success' => true, 'message' => 'Skill deleted successfully'];
        }

        return ['success' => false, 'message' => 'Delete failed'];
    }
}
?>
