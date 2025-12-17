<?php
namespace app\services;

require_once __DIR__ . '/../dao/ContactDAO.php';

class ContactService {
    private $contactDAO;

    public function __construct() {
        $this->contactDAO = new \ContactDAO();
    }

    public function submitContact($data) {
        if (empty($data['name']) || empty($data['email']) || empty($data['message'])) {
            return ['success' => false, 'message' => 'Name, email, and message are required'];
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Invalid email format'];
        }

        $contactData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'message' => $data['message'],
            'status' => 'unread'
        ];

        $contactId = $this->contactDAO->create($contactData);
        
        if ($contactId) {
            return ['success' => true, 'message' => 'Contact message submitted successfully', 'contact_id' => $contactId];
        }

        return ['success' => false, 'message' => 'Failed to submit contact message'];
    }

    public function getContactsByUser($userId) {
        $contacts = $this->contactDAO->findAll();
        
        if (empty($contacts)) {
            return ['success' => true, 'contacts' => []];
        }

        return ['success' => true, 'contacts' => $contacts];
    }

    public function deleteContact($userId, $contactId) {
        $contact = $this->contactDAO->findById($contactId);
        if (!$contact) {
            return ['success' => false, 'message' => 'Contact not found'];
        }

        $success = $this->contactDAO->delete($contactId);
        
        if ($success) {
            return ['success' => true, 'message' => 'Contact deleted successfully'];
        }

        return ['success' => false, 'message' => 'Delete failed'];
    }
}
?>
