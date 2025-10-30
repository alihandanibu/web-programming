<?php
require_once 'dao/UserDAO.php';
require_once 'dao/ProjectDAO.php';
require_once 'dao/SkillDAO.php';
require_once 'dao/ExperienceDAO.php';
require_once 'dao/ContactDAO.php';

echo "=== Testing DAO Classes ===\n\n";

// Test UserDAO
echo "1. Testing UserDAO:\n";
$userDAO = new UserDAO();
$users = $userDAO->findAll();
echo "Found " . count($users) . " users\n";

// Test ProjectDAO
echo "\n2. Testing ProjectDAO:\n";
$projectDAO = new ProjectDAO();
$projects = $projectDAO->findAll();
echo "Found " . count($projects) . " projects\n";

// Test SkillDAO
echo "\n3. Testing SkillDAO:\n";
$skillDAO = new SkillDAO();
$skills = $skillDAO->findAll();
echo "Found " . count($skills) . " skills\n";

// Test ExperienceDAO
echo "\n4. Testing ExperienceDAO:\n";
$experienceDAO = new ExperienceDAO();
$experiences = $experienceDAO->findAll();
echo "Found " . count($experiences) . " experiences\n";

// Test ContactDAO
echo "\n5. Testing ContactDAO:\n";
$contactDAO = new ContactDAO();
$contacts = $contactDAO->findAll();
echo "Found " . count($contacts) . " contacts\n";

echo "\n=== DAO Testing Complete ===\n";
?>