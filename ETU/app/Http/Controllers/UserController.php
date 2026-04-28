<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repository\UserRepositoryInterface;
use App\Http\Requests\UpdatePasswordRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use OpenApi\Attributes as OA;

class UserController extends Controller
{
    private $userRepository;

    public function __construct(UserRepositoryInterface $userRepository){
        $this->userRepository = $userRepository;
    }

    #[OA\Patch(
        path: "/api/updatePassword",
        summary: "update un password (throddling de 5 par minute)",
        tags: ["UserController"],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: [
                new OA\JsonContent(
                    required: ["old_password", "new_password"],
                    properties: [
                        new OA\Property(property: "old_password", type: "string", example: "password1234"),
                        new OA\Property(property: "new_password", type: "string", example: "new_passW0rD"),
                ]
                )
            ]
        ),
        responses: [
            new OA\Response(
                response: 200, description: "Mot de passe mis à jour"
            ),
            new OA\Response(
                response: "422", description: "Données invalides"
            )
        ]
    )]
    public function updatePassword(UpdatePasswordRequest $request)
    {
        try{
            if(!Hash::check($request->input('old_password'), Auth::user()->password)){//https://laravel.com/docs/13.x/hashing#verifying-that-a-password-matches-a-hash
                abort(INVALID_DATA, 'the_password_is_invalid');
            }

            return response()->json($this->userRepository->updateUserPassword($request->input('new_password')), 200);
        }
        catch (ValidationException $ex) {
            abort(INVALID_DATA, "la_validation_ne_passe_pas");
        }
        catch (Exception $ex) {
            abort(SERVER_ERROR, "server_error");
        }
    }

}
