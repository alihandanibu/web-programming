<?php

use OpenApi\Annotations as OA;

class ApiDocs
{
    /**
     * @OA\Post(
     *   path="/auth/register",
     *   tags={"Auth"},
     *   summary="Register a new user",
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       example={"name":"John Doe","email":"john@example.com","password":"password123","role":"user"}
     *     )
     *   ),
     *   @OA\Response(response=200, description="Registered"),
     *   @OA\Response(response=400, description="Validation error")
     * )
     */
    public function register() {}

    /**
     * @OA\Post(
     *   path="/auth/login",
     *   tags={"Auth"},
     *   summary="Login",
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       example={"email":"admin@portfolio.com","password":"password"}
     *     )
     *   ),
     *   @OA\Response(response=200, description="Login successful"),
     *   @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function login() {}

    /**
     * @OA\Post(
     *   path="/auth/verify",
     *   tags={"Auth"},
     *   summary="Verify JWT token",
     *   security={{"bearerAuth":{}}},
     *   @OA\Response(response=200, description="Token valid"),
     *   @OA\Response(response=401, description="Missing/invalid token")
     * )
     */
    public function verify() {}

    /**
     * @OA\Get(
     *   path="/health",
     *   tags={"System"},
     *   summary="Health check",
     *   @OA\Response(response=200, description="OK")
     * )
     */
    public function health() {}

    /**
     * @OA\Get(
     *   path="/users",
     *   tags={"Users"},
     *   summary="Get all users (admin)",
     *   security={{"bearerAuth":{}}},
     *   @OA\Response(response=200, description="OK"),
     *   @OA\Response(response=403, description="Forbidden")
     * )
     */
    public function getUsers() {}

    /**
     * @OA\Get(
     *   path="/users/{id}",
     *   tags={"Users"},
     *   summary="Get user by id (owner or admin)",
     *   security={{"bearerAuth":{}}},
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\Response(response=200, description="OK"),
     *   @OA\Response(response=403, description="Forbidden")
     * )
     */
    public function getUserById() {}

    /**
     * @OA\Put(
     *   path="/users/{id}",
     *   tags={"Users"},
     *   summary="Update user (owner or admin)",
     *   security={{"bearerAuth":{}}},
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(example={"name":"John Doe"})
     *   ),
     *   @OA\Response(response=200, description="Updated"),
     *   @OA\Response(response=403, description="Forbidden")
     * )
     */
    public function updateUser() {}

    /**
     * @OA\Delete(
     *   path="/users/{id}",
     *   tags={"Users"},
     *   summary="Delete user (admin)",
     *   security={{"bearerAuth":{}}},
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\Response(response=200, description="Deleted"),
     *   @OA\Response(response=403, description="Forbidden")
     * )
     */
    public function deleteUser() {}

    /**
     * @OA\Get(
     *   path="/users/{userId}/projects",
     *   tags={"Projects"},
     *   summary="Get projects (owner or admin)",
     *   security={{"bearerAuth":{}}},
     *   @OA\Parameter(name="userId", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\Response(response=200, description="OK"),
     *   @OA\Response(response=403, description="Forbidden")
     * )
     */
    public function getProjects() {}

    /**
     * @OA\Post(
     *   path="/users/{userId}/projects",
     *   tags={"Projects"},
     *   summary="Add project (owner or admin)",
     *   security={{"bearerAuth":{}}},
     *   @OA\Parameter(name="userId", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\RequestBody(required=true, @OA\JsonContent(example={"title":"My Project","description":"..."})),
     *   @OA\Response(response=200, description="Created"),
     *   @OA\Response(response=403, description="Forbidden")
     * )
     */
    public function addProject() {}

    /**
     * @OA\Put(
     *   path="/users/{userId}/projects/{projectId}",
     *   tags={"Projects"},
     *   summary="Update project (owner or admin)",
     *   security={{"bearerAuth":{}}},
     *   @OA\Parameter(name="userId", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\Parameter(name="projectId", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\RequestBody(required=true, @OA\JsonContent(example={"title":"Updated title"})),
     *   @OA\Response(response=200, description="Updated"),
     *   @OA\Response(response=403, description="Forbidden")
     * )
     */
    public function updateProject() {}

    /**
     * @OA\Delete(
     *   path="/users/{userId}/projects/{projectId}",
     *   tags={"Projects"},
     *   summary="Delete project (owner or admin)",
     *   security={{"bearerAuth":{}}},
     *   @OA\Parameter(name="userId", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\Parameter(name="projectId", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\Response(response=200, description="Deleted"),
     *   @OA\Response(response=403, description="Forbidden")
     * )
     */
    public function deleteProject() {}

    /**
     * @OA\Get(
     *   path="/users/{userId}/skills",
     *   tags={"Skills"},
     *   summary="Get skills (owner or admin)",
     *   security={{"bearerAuth":{}}},
     *   @OA\Parameter(name="userId", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\Response(response=200, description="OK"),
     *   @OA\Response(response=403, description="Forbidden")
     * )
     */
    public function getSkills() {}

    /**
     * @OA\Post(
     *   path="/users/{userId}/skills",
     *   tags={"Skills"},
     *   summary="Add skill (owner or admin)",
     *   security={{"bearerAuth":{}}},
     *   @OA\Parameter(name="userId", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\RequestBody(required=true, @OA\JsonContent(example={"name":"PHP","proficiency":"advanced","category":"backend"})),
     *   @OA\Response(response=200, description="Created"),
     *   @OA\Response(response=403, description="Forbidden")
     * )
     */
    public function addSkill() {}

    /**
     * @OA\Put(
     *   path="/users/{userId}/skills/{skillId}",
     *   tags={"Skills"},
     *   summary="Update skill (owner or admin)",
     *   security={{"bearerAuth":{}}},
     *   @OA\Parameter(name="userId", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\Parameter(name="skillId", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\RequestBody(required=true, @OA\JsonContent(example={"proficiency":"expert"})),
     *   @OA\Response(response=200, description="Updated"),
     *   @OA\Response(response=403, description="Forbidden")
     * )
     */
    public function updateSkill() {}

    /**
     * @OA\Delete(
     *   path="/users/{userId}/skills/{skillId}",
     *   tags={"Skills"},
     *   summary="Delete skill (owner or admin)",
     *   security={{"bearerAuth":{}}},
     *   @OA\Parameter(name="userId", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\Parameter(name="skillId", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\Response(response=200, description="Deleted"),
     *   @OA\Response(response=403, description="Forbidden")
     * )
     */
    public function deleteSkill() {}

    /**
     * @OA\Get(
     *   path="/users/{userId}/experiences",
     *   tags={"Experiences"},
     *   summary="Get experiences (owner or admin)",
     *   security={{"bearerAuth":{}}},
     *   @OA\Parameter(name="userId", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\Response(response=200, description="OK"),
     *   @OA\Response(response=403, description="Forbidden")
     * )
     */
    public function getExperiences() {}

    /**
     * @OA\Post(
     *   path="/users/{userId}/experiences",
     *   tags={"Experiences"},
     *   summary="Add experience (owner or admin)",
     *   security={{"bearerAuth":{}}},
     *   @OA\Parameter(name="userId", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\RequestBody(required=true, @OA\JsonContent(example={"company":"ACME","position":"Intern","start_date":"2024-01-01"})),
     *   @OA\Response(response=200, description="Created"),
     *   @OA\Response(response=403, description="Forbidden")
     * )
     */
    public function addExperience() {}

    /**
     * @OA\Put(
     *   path="/users/{userId}/experiences/{experienceId}",
     *   tags={"Experiences"},
     *   summary="Update experience (owner or admin)",
     *   security={{"bearerAuth":{}}},
     *   @OA\Parameter(name="userId", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\Parameter(name="experienceId", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\RequestBody(required=true, @OA\JsonContent(example={"description":"Did stuff"})),
     *   @OA\Response(response=200, description="Updated"),
     *   @OA\Response(response=403, description="Forbidden")
     * )
     */
    public function updateExperience() {}

    /**
     * @OA\Delete(
     *   path="/users/{userId}/experiences/{experienceId}",
     *   tags={"Experiences"},
     *   summary="Delete experience (owner or admin)",
     *   security={{"bearerAuth":{}}},
     *   @OA\Parameter(name="userId", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\Parameter(name="experienceId", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\Response(response=200, description="Deleted"),
     *   @OA\Response(response=403, description="Forbidden")
     * )
     */
    public function deleteExperience() {}

    /**
     * @OA\Post(
     *   path="/contact",
     *   tags={"Contact"},
     *   summary="Submit contact message (public)",
     *   @OA\RequestBody(required=true, @OA\JsonContent(example={"name":"A","email":"a@b.com","message":"Hello"})),
     *   @OA\Response(response=200, description="Submitted"),
     *   @OA\Response(response=400, description="Validation error")
     * )
     */
    public function contactSubmit() {}

    /**
     * @OA\Get(
     *   path="/users/{userId}/contacts",
     *   tags={"Contact"},
     *   summary="Get contacts (admin)",
     *   security={{"bearerAuth":{}}},
     *   @OA\Parameter(name="userId", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\Response(response=200, description="OK"),
     *   @OA\Response(response=403, description="Forbidden")
     * )
     */
    public function contactList() {}

    /**
     * @OA\Delete(
     *   path="/users/{userId}/contacts/{contactId}",
     *   tags={"Contact"},
     *   summary="Delete contact (admin)",
     *   security={{"bearerAuth":{}}},
     *   @OA\Parameter(name="userId", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\Parameter(name="contactId", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\Response(response=200, description="Deleted"),
     *   @OA\Response(response=403, description="Forbidden")
     * )
     */
    public function contactDelete() {}
}
