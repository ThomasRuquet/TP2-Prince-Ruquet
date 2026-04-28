<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Repository\RentalRepositoryInterface;
use OpenApi\Attributes as OA;

class UserRentalController extends Controller
{
    private $rentalRepository;

    public function __construct(RentalRepositoryInterface $rentalRepository){
        $this->rentalRepository = $rentalRepository;
    }

    /**
     * Display a listing of the resource.
     */
    #[OA\Get(
        path: "/api/locations",
        summary: "shows all active locations of user (throddling de 5 par minute)",
        tags: ["UserRentalController"],
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(
                response: 200, description: "les locations ont été retournées avec succès"
            ),
            new OA\Response(
                response: 401, description: "l'utilisateur n'est pas connecté"
            ),
            new OA\Response(
                response: 422, description: "Données invalides"
            )
        ]
    )]
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
