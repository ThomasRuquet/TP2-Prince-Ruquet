<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Repository\UserRepositoryInterface;
use App\Repository\ReviewRepositoryInterface;
use App\Http\Requests\CreateReviewRequest;
use App\Http\Requests\StoreReviewRequest;
use OpenApi\Attributes as OA;

class ReviewController extends Controller
{
    private $userRepository, $reviewRepository;

    public function __construct(UserRepositoryInterface $userRepository, ReviewRepositoryInterface $reviewRepository){
        $this->userRepository = $userRepository;
        $this->reviewRepository = $reviewRepository;
    }

    #[OA\Post(
        path: "/api/createReview",
        summary: "Créer une review (throddling de 5 par minute)",
        tags: ["ReviewController"],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: [
                new OA\JsonContent(
                    required: ["rating", "userId", "rentalId"],
                    properties: [
                        new OA\Property(property: "rating", type: "integer", example: 4),
                        new OA\Property(property: "comment", type: "string", example: "yo!"),
                        new OA\Property(property: "userId", type: "integer", example: "2"),
                        new OA\Property(property: "rentalId", type: "integer", example: "3")
                ]
                )
            ]
        ),
        responses: [
            new OA\Response(
                response: "201", description: "review créée"
            ),
            new OA\Response(
                response: "422", description: "Données invalides"
            )
        ]
    )]
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

}
