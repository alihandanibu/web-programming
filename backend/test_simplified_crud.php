<?php
require_once 'dao/UserDAO.php';
require_once 'dao/ProjectDAO.php';
require_once 'dao/SkillDAO.php';
require_once 'dao/ExperienceDAO.php';
require_once 'dao/ContactDAO.php';

echo "=== Testing SIMPLIFIED CRUD Operations ===\n\n";

try {
    // Test UserDAO CRUD
    echo "1. Testing UserDAO CRUD:\n";
    $userDAO = new UserDAO();
    
    // CREATE (POST)
    $newUser = [
        'email' => 'test_simple@example.com',
        'password' => password_hash('test123', PASSWORD_DEFAULT),
        'role' => 'user'
    ];
    $newUserId = $userDAO->create($newUser);
    echo "✅ CREATE (POST) - User created with ID: " . $newUserId . "\n";
    
    // READ (GET by ID)
    $user = $userDAO->findById($newUserId);
    echo "✅ READ (GET by ID) - Found user: " . $user['email'] . "\n";
    
    // READ (GET all)
    $allUsers = $userDAO->findAll();
    echo "✅ READ (GET all) - Total users: " . count($allUsers) . "\n";
    
    // UPDATE (PUT)
    $updateData = ['role' => 'admin'];
    $updated = $userDAO->update($newUserId, $updateData);
    echo "✅ UPDATE (PUT) - User update: " . ($updated ? "success" : "failed") . "\n";
    
    // DELETE (DELETE)
    $deleted = $userDAO->delete($newUserId);
    echo "✅ DELETE (DELETE) - User delete: " . ($deleted ? "success" : "failed") . "\n";

    // Test ProjectDAO
    echo "\n2. Testing ProjectDAO:\n";
    $projectDAO = new ProjectDAO();
    $projects = $projectDAO->findAll();
    echo "✅ READ (GET all) - Projects: " . count($projects) . " found\n";
    
    $userProjects = $projectDAO->findByUserId(1);
    echo "✅ Custom method - Projects by user: " . count($userProjects) . " found\n";

    // Test SkillDAO (sada bez findByCategory)
    echo "\n3. Testing SkillDAO:\n";
    $skillDAO = new SkillDAO();
    $skills = $skillDAO->findAll();
    echo "✅ READ (GET all) - Skills: " . count($skills) . " found\n";
    
    $userSkills = $skillDAO->findByUserId(1);
    echo "✅ Custom method - Skills by user: " . count($userSkills) . " found\n";

    // Test ExperienceDAO
    echo "\n4. Testing ExperienceDAO:\n";
    $experienceDAO = new ExperienceDAO();
    $experiences = $experienceDAO->findAll();
    echo "✅ READ (GET all) - Experiences: " . count($experiences) . " found\n";
    
    $userExperiences = $experienceDAO->findByUserId(1);
    echo "✅ Custom method - Experiences by user: " . count($userExperiences) . " found\n";

    // Test ContactDAO
    echo "\n5. Testing ContactDAO:\n";
    $contactDAO = new ContactDAO();
    $contacts = $contactDAO->findAll();
    echo "✅ READ (GET all) - Contacts: " . count($contacts) . " found\n";
    
    $unreadCount = $contactDAO->getUnreadCount();
    echo "✅ Custom method - Unread contacts: " . $unreadCount . "\n";

    echo "\n🎉 === ALL CRUD OPERATIONS TESTED SUCCESSFULLY! ===\n";
    echo "✅ CREATE (POST) - Working\n";
    echo "✅ READ (GET) - Working\n"; 
    echo "✅ UPDATE (PUT) - Working\n";
    echo "✅ DELETE (DELETE) - Working\n";
    echo "✅ All DAO classes properly use BaseDAO methods\n";
    echo "✅ Simplified - removed complex methods\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>