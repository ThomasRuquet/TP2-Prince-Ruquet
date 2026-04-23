<?php

namespace App\Http\Controllers;

use App\Http\Resources\EquipmentRessource;
use Exception;
use App\Http\Requests\StoreEquipmentRequest;
use App\Repository\EquipmentRepositoryInterface;

class EquipmentController extends Controller
{
    public function __construct(private EquipmentRepositoryInterface $equipmentRepository)
    {
    }

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

    public function update(StoreEquipmentRequest $request, $id)
    {
        try {
            $equipment = $this->equipmentRepository->updateById((int) $id, $request->validated());

            return (new EquipmentRessource($equipment))
                ->response()
                ->setStatusCode(OK);
        } catch (Exception $e) {
            abort(500, SERVER_ERROR . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $this->equipmentRepository->deleteById((int) $id);
            return response()->json(null, NO_CONTENT);
        } catch (Exception $e) {
            abort(500, SERVER_ERROR . $e->getMessage());
        }
    }
}
