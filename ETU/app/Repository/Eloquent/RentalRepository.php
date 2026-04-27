<?php

namespace App\Repository\Eloquent;

use App\Repository\RentalRepositoryInterface;
use App\Repository\Eloquent\BaseRepository;
use App\Models\Rental;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class RentalRepository extends BaseRepository implements RentalRepositoryInterface
{
    public function __construct(Rental $model)
    {
        parent::__construct($model);
    }

    public function findCurrentUserActiveRental(){
        return DB::table('rentals')
            ->where('user_id', auth()->user()->id)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->paginate(5);
    }
}
