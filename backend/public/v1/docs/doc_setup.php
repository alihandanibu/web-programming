<?php

/**
 * @OA\Info(
 *     title="Handan Portfolio API",
 *     version="1.0.0",
 *     description="RESTful API for Handan's Portfolio - Milestone 3",
 *     contact={
 *         "email": "alihandan@stu.ibu.edu.ba"
 *     }
 * )
 * 
 * @OA\Server(
 *     url="http://localhost/mojnoviprojekat/web-programming/backend",
 *     description="Development server"
 * )
 * 
 * @OA\SecurityScheme(
 *     type="http",
 *     description="Login with username and password to get the authentication token",
 *     name="Token based based security",
 *     in="header",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     securityScheme="bearerAuth",
 * )
 */
?>
