<?php
namespace app\services;

require_once __DIR__ . '/../dao/ContactDAO.php';

class ContactService {
    private $contactDAO;
    private const ALLOWED_STATUSES = ['unread', 'read', 'replied'];

    public function __construct() {
        $this->contactDAO = new \ContactDAO();
    }

    public function submitContact($data) {
        $name = trim((string)($data['name'] ?? ''));
        $email = trim((string)($data['email'] ?? ''));
        $message = trim((string)($data['message'] ?? ''));
        $subject = trim((string)($data['subject'] ?? ''));

        if ($name === '' || $email === '' || $message === '') {
            return ['success' => false, 'message' => 'Name, email, and message are required'];
        }

        if (strlen($name) < 2 || strlen($name) > 100) {
            return ['success' => false, 'message' => 'Name must be between 2 and 100 characters'];
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Invalid email format'];
        }

        if (strlen($message) < 5 || strlen($message) > 5000) {
            return ['success' => false, 'message' => 'Message must be between 5 and 5000 characters'];
        }

        // Prepend subject to message if provided
        $finalMessage = $message;
        if ($subject !== '' && stripos($message, 'Subject:') !== 0) {
            $finalMessage = "Subject: {$subject}\n\n{$message}";
        }

        $contactData = [
            'name' => $name,
            'email' => $email,
            'message' => $finalMessage,
            'status' => 'unread'
        ];

        $contactId = $this->contactDAO->create($contactData);
        
        if ($contactId) {
            return ['success' => true, 'message' => 'Contact message submitted successfully', 'contact_id' => $contactId];
        }

        return ['success' => false, 'message' => 'Failed to submit contact message'];
    }

    public function getContacts($status = null) {
        if ($status !== null && !in_array($status, self::ALLOWED_STATUSES, true)) {
            return ['success' => false, 'message' => 'Invalid status filter'];
        }

        if ($status !== null) {
            $contacts = $this->contactDAO->findByStatus($status);
        } else {
            $contacts = $this->contactDAO->findAll();
        }
        
        return ['success' => true, 'contacts' => $contacts ?: []];
    }

    public function getContactsByUser($userId, $status = null) {
        // userId is ignored - contacts are global, but kept for route compatibility
        return $this->getContacts($status);
    }

    public function updateContactStatus($contactId, $status) {
        if (!in_array($status, self::ALLOWED_STATUSES, true)) {
            return ['success' => false, 'message' => 'Invalid status. Must be: unread, read, or replied'];
        }

        $contact = $this->contactDAO->findById($contactId);
        if (!$contact) {
            return ['success' => false, 'message' => 'Contact not found'];
        }

        $updated = $this->contactDAO->update($contactId, ['status' => $status]);
        
        if ($updated) {
            return ['success' => true, 'message' => "Contact marked as {$status}"];
        }

        return ['success' => false, 'message' => 'Failed to update contact status'];
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
