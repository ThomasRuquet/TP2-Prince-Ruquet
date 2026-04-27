<?php

namespace App\Repository;

use App\Repository\RepositoryInterface;

interface UserRepositoryInterface extends RepositoryInterface
{
    public function hasUserAlreadyReviewedEquipment(int $equipmentId);
}
