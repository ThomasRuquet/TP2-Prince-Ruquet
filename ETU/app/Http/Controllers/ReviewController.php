<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Repository\UserRepositoryInterface;
use App\Repository\ReviewRepositoryInterface;
use App\Http\Requests\CreateReviewRequest;
use App\Http\Requests\StoreReviewRequest;

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
    public function store(StoreReviewRequest $request)
    {
        try{
            $isReviewOnAnEquipmentAlreadyReviewed = $this->userRepository->hasUserAlreadyReviewedEquipment($request->input('rental_id'));

            if($isReviewOnAnEquipmentAlreadyReviewed){
                abort(INVALID_DATA, "USER_ALREADY_REVIEWED_THAT_EQUIPMENT");
            }

            $review = $this->reviewRepository->create([...$request->validated(), 'user_id' => auth()->id()]);

            return response()->json($review, 201);
        }
        catch (ValidationException $ex) {
            abort(INVALID_DATA, "la_validation_ne_passe_pas");
        }
        catch (Exception $ex) {
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
