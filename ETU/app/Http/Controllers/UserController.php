<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repository\UserRepositoryInterface;
use App\Http\Requests\UpdatePasswordRequest;

class UserController extends Controller
{
    private $userRepository;

    public function __construct(UserRepositoryInterface $userRepository){
        $this->userRepository = $userRepository;
    }

    /**
     * Update the specified resource in storage.
     */
    public function updatePassword(UpdatePasswordRequest $request)
    {
        try{
            $this->userRepository->updateUserPassword($request->input('password'));
        }
        catch (ValidationException $ex) {
            abort(INVALID_DATA, "la_validation_ne_passe_pas");
        }
        catch (Exception $ex) {
            abort(SERVER_ERROR, "server_error");
        }
    }

}
