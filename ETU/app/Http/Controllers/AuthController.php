<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Http\Request;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use OpenApi\Attributes as OA;

class AuthController extends Controller
{
    #[OA\Post(
        path: '/api/signup',
        summary: 'Inscription d\'un utilisateur',
        description: 'Crée un utilisateur',
        tags: ['Auth'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['first_name', 'last_name', 'login', 'email', 'password'],
                properties: [
                    new OA\Property(property: 'first_name', type: 'string', example: 'User'),
                    new OA\Property(property: 'last_name', type: 'string', example: 'Example'),
                    new OA\Property(property: 'login', type: 'string', example: 'UserExample'),
                    new OA\Property(property: 'phone', type: 'string', example: '1234567890'),
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'user@example.com'),
                    new OA\Property(property: 'password', type: 'string', format: 'password', example: 'user123456')
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Utilisateur inscrit (throttling: 5 requêtes par minute)',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'User registered successfully')
                    ]
                )
            ),
            new OA\Response(response: 422, description: 'Données invalides'),
            new OA\Response(response: 500, description: 'Erreur serveur')
        ]
    )]
    public function register(RegisterRequest $request)
    {
        try {
            User::create([...$request->all(), 'role_id' => 1]);

            return response()->json([
                'message' => 'User registered successfully'
            ], 201);
        } catch (Exception $e) {
            abort(SERVER_ERROR, 'Registration failed: ' . $e->getMessage());
        }
    }

    #[OA\Post(
        path: '/api/signin',
        summary: 'Connexion utilisateur',
        description: 'Authentifie un utilisateur. Throttling: 5 requêtes par minute.',
        tags: ['Auth'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['login', 'password'],
                properties: [
                    new OA\Property(property: 'login', type: 'string', example: 'UserExample'),
                    new OA\Property(property: 'password', type: 'string', format: 'password', example: 'user123456')
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Connexion réussie',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'access_token', type: 'string', example: '1|oaiwehfoiwhe23423')
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Identifiants invalides'),
            new OA\Response(response: 422, description: 'Données invalides')
        ]
    )]
    public function login(LoginRequest $request)
    {
        if (!Auth::attempt($request->validated())) {
            abort(UNAUTHORIZED, 'Invalid credentials');
        }

        $user = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
        ], 200);
    }
    #[OA\Post(
        path: '/api/signout',
        summary: 'Déconnexion utilisateur',
        description: 'Révoque le token.',
        tags: ['Auth'],
        //https://swagger.io/docs/specification/v3_0/authentication/
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Déconnexion réussie. Throttling: 5 requêtes par minute',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Logged out successfully')
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Non authentifié'),
            new OA\Response(response: 500, description: 'Erreur serveur')
        ]
    )]
    public function logout(Request $request)
    {
        try {
            //https://laravel.com/docs/13.x/sanctum#revoking-tokens
            $request->user()->currentAccessToken()->delete();
            return response()->json([
                'message' => 'Logged out successfully'
            ], 200);
        } catch (Exception $e) {
            abort(SERVER_ERROR, 'Authentication failed: ' . $e->getMessage());
        }
    }

    #[OA\Post(
        path: '/api/refresh',
        summary: 'Rafraîchir le token',
        description: 'Supprime le token actuel et en génère un nouveau. Throttling: 5 requêtes par minute.',
        tags: ['Auth'],
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Nouveau token généré',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'access_token', type: 'string', example: '2|ijerg3424jv')
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Non authentifié'),
            new OA\Response(response: 500, description: 'Erreur serveur')
        ]
    )]
    public function refresh(Request $request)
    {
        try {
            $user = $request->user();
            $user->currentAccessToken()->delete();
            $newToken = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'access_token' => $newToken,
            ], 200);
        } catch (Exception $e) {
            abort(SERVER_ERROR, 'Authentication failed: ' . $e->getMessage());
        }
    }
    #[OA\Get(
        path: '/api/me',
        summary: 'Profil utilisateur connecté',
        description: 'Retourne les informations de l\'utilisateur authentifié. Throttling: 5 requêtes par minute.',
        tags: ['Auth'],
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Profil récupéré',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'user',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'id', type: 'integer', example: 1),
                                new OA\Property(property: 'name', type: 'string', example: 'Exemple User'),
                                new OA\Property(property: 'login', type: 'string', example: 'UserExample'),
                                new OA\Property(property: 'email', type: 'string', format: 'email', example: 'user@example.com')
                            ]
                        )
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Non authentifié'),
            new OA\Response(response: 500, description: 'Erreur serveur')
        ]
    )]
    public function me(Request $request)
    {
        try {
            return response()->json([
                'user' => $request->user(),
            ], 200);
        } catch (Exception $e) {
            abort(SERVER_ERROR, 'Authentication failed: ' . $e->getMessage());
        }
    }
}
