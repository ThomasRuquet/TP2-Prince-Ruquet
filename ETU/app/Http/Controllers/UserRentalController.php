<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserRentalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            if(Auth::check()){
                return DB::table('rentals')->where('user_id', auth()->user())->get();
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
