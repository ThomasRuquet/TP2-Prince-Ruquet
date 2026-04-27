<?php

namespace App\Http\Controllers;

use App\Http\Resources\EquipmentRessource;
use Exception;
use App\Http\Requests\StoreEquipmentRequest;
use App\Repository\EquipmentRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use OpenApi\Attributes as OA;

class EquipmentController extends Controller
{
    public function __construct(private EquipmentRepositoryInterface $equipmentRepository)
    {
    }

    #[OA\Post(
        path: '/api/equipment',
        summary: 'Créer un équipement',
        description: 'Crée un nouvel équipement',
        tags: ['Equipment'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'description', 'daily_price', 'category_id'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'test name'),
                    new OA\Property(property: 'description', type: 'string', example: 'test description'),
                    new OA\Property(property: 'daily_price', type: 'number', format: 'float', example: 12.5),
                    new OA\Property(property: 'category_id', type: 'integer', example: 1)
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Équipement créé',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'name', type: 'string', example: 'test name'),
                        new OA\Property(property: 'description', type: 'string', example: 'test description'),
                        new OA\Property(property: 'dailyPrice', type: 'number', format: 'float', example: 12.5),
                        new OA\Property(property: 'category_id', type: 'integer', example: 1)
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Non authentifié'),
            new OA\Response(response: 403, description: 'Accès refusé'),
            new OA\Response(response: 422, description: 'Données invalides'),
            new OA\Response(response: 500, description: 'Erreur serveur')
        ]
    )]
    public function store(StoreEquipmentRequest $request)
    {
        try {
            $equipment = $this->equipmentRepository->create($request->validated());
            return (new EquipmentRessource($equipment))
                ->response()
                ->setStatusCode(CREATED);
        } catch (Exception $e) {
            abort(500, SERVER_ERROR . $e->getMessage());
        }
    }

    #[OA\Put(
        path: '/api/equipment/{id}',
        summary: 'Modifier un équipement',
        description: 'Met à jour un équipement existant',
        tags: ['Equipment'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'Identifiant de l\'équipement',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer', example: 1)
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'description', 'daily_price', 'category_id'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'test name'),
                    new OA\Property(property: 'description', type: 'string', example: 'test description'),
                    new OA\Property(property: 'daily_price', type: 'number', format: 'float', example: 15),
                    new OA\Property(property: 'category_id', type: 'integer', example: 1)
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Équipement mis à jour',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'name', type: 'string', example: 'test name'),
                        new OA\Property(property: 'description', type: 'string', example: 'test description'),
                        new OA\Property(property: 'dailyPrice', type: 'number', format: 'float', example: 15),
                        new OA\Property(property: 'category_id', type: 'integer', example: 1)
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Non authentifié'),
            new OA\Response(response: 403, description: 'Accès refusé'),
            new OA\Response(response: 404, description: 'Équipement introuvable'),
            new OA\Response(response: 422, description: 'Données invalides'),
            new OA\Response(response: 500, description: 'Erreur serveur')
        ]
    )]
    public function update(StoreEquipmentRequest $request, $id)
    {
        try {
            $equipment = $this->equipmentRepository->updateById((int) $id, $request->validated());

            return (new EquipmentRessource($equipment))
                ->response()
                ->setStatusCode(OK);
        } catch (ModelNotFoundException $e) {
            abort(404, 'Equipment not found');
        } catch (Exception $e) {
            abort(500, SERVER_ERROR . $e->getMessage());
        }
    }

    #[OA\Delete(
        path: '/api/equipment/{id}',
        summary: 'Supprimer un équipement',
        description: 'Supprime un équipement existant',
        tags: ['Equipment'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'Identifiant de l\'équipement',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer', example: 1)
            )
        ],
        responses: [
            new OA\Response(response: 204, description: 'Équipement supprimé'),
            new OA\Response(response: 401, description: 'Non authentifié'),
            new OA\Response(response: 403, description: 'Accès refusé'),
            new OA\Response(response: 404, description: 'Équipement introuvable'),
            new OA\Response(response: 500, description: 'Erreur serveur')
        ]
    )]
    public function destroy($id)
    {
        try {
            $this->equipmentRepository->deleteById((int) $id);
            return response()->json(null, NO_CONTENT);
        } catch (ModelNotFoundException $e) {
            abort(404, 'Equipment not found');
        } catch (Exception $e) {
            abort(500, SERVER_ERROR . $e->getMessage());
        }
    }
}
