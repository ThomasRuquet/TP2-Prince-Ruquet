<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReviewController extends Controller
{
    private $userRepository, $reviewRepository;

    public function __construct(UserRepositoryInterface $userRepository, ReviewRepositoryInterface $reviewRepository){
        $this->userRepository = $userRepository;
        $this->reviewRepository = $reviewRepository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //TODO: review request validation

        try{
            if (Auth::check()) { //C'est inutile car le path est déjà dans le middleware auth:sanctum mais au cas où quelqu'un ferait des modification au paths et oublierait de les remettre dans le middleware.
                $isReviewOnAnEquipmentAlreadyReviewed = $userRepository->hasUserAlreadyReviewedEquipment($request->query('equipmentId'));

                if($isReviewOnAnEquipmentAlreadyReviewed){
                    abort(INVALID_DATA, "USER_ALREADY_REVIEWED_THAT_EQUIPMENT");
                }

                $review = Review::create($request);

                return ($review)->response()->setStatusCode(201);
            }
            else{
                abort(UNAUTHORISED, "USER_NOT_AUTHENTICATED");
            }
        }
        catch(Exception $ex){
            abort(SERVER_ERROR, "server_error");
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
