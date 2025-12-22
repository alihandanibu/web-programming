<?php
namespace app\services;

require_once __DIR__ . '/../dao/ExperienceDAO.php';

class ExperienceService {
    private $experienceDAO;

    public function __construct() {
        $this->experienceDAO = new \ExperienceDAO();
    }

    public function addExperience($userId, $data) {
        if (empty($data['company']) || empty($data['position']) || empty($data['start_date'])) {
            return ['success' => false, 'message' => 'Company, position and start_date are required'];
        }

        $experienceData = [
            'user_id' => $userId,
            'company' => $data['company'],
            'position' => $data['position'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'] ?? null,
            'description' => $data['description'] ?? null
        ];

        if (isset($data['current_job'])) {
            $experienceData['current_job'] = (int)(bool)$data['current_job'];
        }

        $experienceId = $this->experienceDAO->create($experienceData);
        
        if ($experienceId) {
            return ['success' => true, 'message' => 'Experience added successfully', 'experience_id' => $experienceId];
        }

        return ['success' => false, 'message' => 'Failed to add experience'];
    }

    public function getExperienceByUser($userId) {
        $experiences = $this->experienceDAO->findByUserId($userId);
        
        if (empty($experiences)) {
            return ['success' => true, 'experiences' => []];
        }

        return ['success' => true, 'experiences' => $experiences];
    }

    public function updateExperience($userId, $experienceId, $data) {
        $experience = $this->experienceDAO->findById($experienceId);
        
        if (!$experience || $experience['user_id'] != $userId) {
            return ['success' => false, 'message' => 'Experience not found'];
        }

        $allowedFields = ['company', 'position', 'start_date', 'end_date', 'description', 'current_job'];
        $updateData = array_intersect_key($data, array_flip($allowedFields));
        if (isset($updateData['current_job'])) {
            $updateData['current_job'] = (int)(bool)$updateData['current_job'];
        }

        if (empty($updateData)) {
            return ['success' => false, 'message' => 'No valid fields to update'];
        }

        $success = $this->experienceDAO->update($experienceId, $updateData);
        
        if ($success) {
            return ['success' => true, 'message' => 'Experience updated successfully'];
        }

        return ['success' => false, 'message' => 'Update failed'];
    }

    public function deleteExperience($userId, $experienceId) {
        $experience = $this->experienceDAO->findById($experienceId);
        
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