<?php

namespace App\Repository;

use App\Repository\RepositoryInterface;

interface RentalRepositoryInterface extends RepositoryInterface
{
    public function findCurrentUserActiveRental();
}
