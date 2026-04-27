<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repository\UserRepositoryInterface;
use App\Http\Requests\UpdatePasswordRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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
