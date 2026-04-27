<?php

namespace App\Repository\Eloquent;

use App\Models\Equipment;
use App\Repository\EquipmentRepositoryInterface;

class EquipmentRepository extends BaseRepository implements EquipmentRepositoryInterface
{
    public function __construct()
    {
        parent::__construct(Equipment::class);
    }

    public function updateById(int $id, array $attributes): Equipment
    {
        $equipment = $this->model->findOrFail($id);
        $equipment->update($attributes);

        return $equipment;
    }

    public function deleteById(int $id): void
    {
        $equipment = $this->model->findOrFail($id);
        $equipment->delete();
    }
}
