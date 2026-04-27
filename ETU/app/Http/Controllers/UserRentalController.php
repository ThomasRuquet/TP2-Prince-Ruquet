<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Repository\RentalRepositoryInterface;

class UserRentalController extends Controller
{
    private $rentalRepository;

    public function __construct(RentalRepositoryInterface $rentalRepository){
        $this->rentalRepository = $rentalRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function getActiveRentals()
    {
        try{
            if(Auth::check()){
                return response()->json($this->rentalRepository->findCurrentUserActiveRental(), OK);
            }
            else{
                abort(UNAUTHORIZED, "USER_IS_NOT_LOGGED_IN");
            }
        }
        catch(Exception $ex){
            abort(SERVER_ERROR, "server_error");
        }
    }
}
