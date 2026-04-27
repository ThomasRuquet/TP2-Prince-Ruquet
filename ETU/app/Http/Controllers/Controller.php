<?php

namespace App\Http\Controllers;
use OpenApi\Attributes as OA;

define("OK", 200);
define("CREATED", 201);
define("NO_CONTENT", 204);
define('UNAUTHORIZED', 401);
define("NOT_FOUND", 404);
define("INVALID_DATA", 422);
define("SERVER_ERROR", 500);

define("SONGS_PAGINATION", 10);

#[OA\Info(title: "API Auth", version: "1.0")]
#[OA\Server(url: "http://localhost:8000", description: "Serveur local")]
//https://swagger.io/docs/specification/v3_0/authentication/bearer-authentication/
#[OA\SecurityScheme(
    securityScheme: "bearerAuth",
    type: "http",
    scheme: "bearer",
    bearerFormat: "Token Sanctum",
    description: "Utiliser Authorization: Bearer {token}"
)]

abstract class Controller
{
    //
}
