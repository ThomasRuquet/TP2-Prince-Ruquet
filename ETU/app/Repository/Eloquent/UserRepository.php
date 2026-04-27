<?php

namespace App\Repository\Eloquent;

use App\Repository\UserRepositoryInterface;
use App\Repository\Eloquent\BaseRepository;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    public function hasUserAlreadyReviewedEquipment(int $rentalId){
        return DB::table('reviews')
            ->join('rentals', 'reviews.rental_id', '=', 'rentals.id') //https://laravel.com/docs/12.x/queries#inner-join-clause
            ->where('reviews.user_id', auth::user()->id)
            ->where('rentals.equipment_id', $rentalId)
            ->exists();
    }
}
