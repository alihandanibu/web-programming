<?php
namespace app\services;

require_once __DIR__ . '/../dao/ProjectDAO.php';

class ProjectService {
    private $projectDAO;

    public function __construct() {
        $this->projectDAO = new \ProjectDAO();
    }

    public function addProject($userId, $data) {
        if (empty($data['title'])) {
            return ['success' => false, 'message' => 'Project title is required'];
        }

        $projectData = [
            'user_id' => $userId,
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'image_url' => $data['image_url'] ?? null,
            'project_url' => $data['project_url'] ?? ($data['link'] ?? null),
            'github_url' => $data['github_url'] ?? null
        ];

        $projectId = $this->projectDAO->create($projectData);
        
        if ($projectId) {
            return ['success' => true, 'message' => 'Project added successfully', 'project_id' => $projectId];
        }

        return ['success' => false, 'message' => 'Failed to add project'];
    }

    public function getProjectsByUser($userId) {
        $projects = $this->projectDAO->findByUserId($userId);
        
        if (empty($projects)) {
            return ['success' => true, 'projects' => []];
        }

        return ['success' => true, 'projects' => $projects];
    }

    public function updateProject($userId, $projectId, $data) {
        $project = $this->projectDAO->findById($projectId);
        
        if (!$project || $project['user_id'] != $userId) {
            return ['success' => false, 'message' => 'Project not found'];
        }

        $allowed = ['title', 'description', 'image_url', 'project_url', 'github_url'];
        $updateData = array_intersect_key($data, array_flip($allowed));
        if (isset($updateData['link']) && !isset($updateData['project_url'])) {
            $updateData['project_url'] = $updateData['link'];
            unset($updateData['link']);
        }

        if (empty($updateData)) {
            return ['success' => false, 'message' => 'No valid fields to update'];
        }

        $success = $this->projectDAO->update($projectId, $updateData);
        
        if ($success) {
            return ['success' => true, 'message' => 'Project updated successfully'];
        }

        return ['success' => false, 'message' => 'Update failed'];
    }

    public function deleteProject($userId, $projectId) {
        $project = $this->projectDAO->findById($projectId);
        
        if (!$project || $project['user_id'] != $userId) {
            return ['success' => false, 'message' => 'Project not found'];
        }

        $success = $this->projectDAO->delete($projectId);
        
        if ($success) {
            return ['success' => true, 'message' => 'Project deleted successfully'];
        }

        return ['success' => false, 'message' => 'Delete failed'];
    }
}
?>
