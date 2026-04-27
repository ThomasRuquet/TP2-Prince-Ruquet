<?php

namespace App\Repository;

use App\Repository\RepositoryInterface;
use App\Models\Equipment;

interface EquipmentRepositoryInterface extends RepositoryInterface
{
	public function updateById(int $id, array $attributes): Equipment;
	public function deleteById(int $id): void;
}
