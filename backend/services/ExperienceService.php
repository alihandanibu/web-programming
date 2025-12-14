<?php
namespace app\services;

use app\dao\ExperienceDAO;

class ExperienceService {
    private $experienceDAO;

    public function __construct() {
        $this->experienceDAO = new ExperienceDAO();
    }

    public function addExperience($userId, $data) {
        if (empty($data['company']) || empty($data['position'])) {
            return ['success' => false, 'message' => 'Company and position are required'];
        }

        $experienceData = [
            'user_id' => $userId,
            'company' => $data['company'],
            'position' => $data['position'],
            'start_date' => $data['start_date'] ?? null,
            'end_date' => $data['end_date'] ?? null,
            'description' => $data['description'] ?? null
        ];

        $experienceId = $this->experienceDAO->create($experienceData);
        
        if ($experienceId) {
            return ['success' => true, 'message' => 'Experience added successfully', 'experience_id' => $experienceId];
        }

        return ['success' => false, 'message' => 'Failed to add experience'];
    }

    public function getExperienceByUser($userId) {
        $experiences = $this->experienceDAO->getByUserId($userId);
        
        if (empty($experiences)) {
            return ['success' => true, 'experiences' => []];
        }

        return ['success' => true, 'experiences' => $experiences];
    }

    public function updateExperience($userId, $experienceId, $data) {
        $experience = $this->experienceDAO->read($experienceId);
        
        if (!$experience || $experience['user_id'] != $userId) {
            return ['success' => false, 'message' => 'Experience not found'];
        }

        $success = $this->experienceDAO->update($experienceId, $data);
        
        if ($success) {
            return ['success' => true, 'message' => 'Experience updated successfully'];
        }

        return ['success' => false, 'message' => 'Update failed'];
    }

    public function deleteExperience($userId, $experienceId) {
        $experience = $this->experienceDAO->read($experienceId);
        
        if (!$experience || $experience['user_id'] != $userId) {
            return ['success' => false, 'message' => 'Experience not found'];
        }

        $success = $this->experienceDAO->delete($experienceId);
        
        if ($success) {
            return ['success' => true, 'message' => 'Experience deleted successfully'];
        }

        return ['success' => false, 'message' => 'Delete failed'];
    }
}
?>