<?php

namespace App\Repository\Eloquent;

use App\Repository\UserRepositoryInterface;
use App\Repository\Eloquent\BaseRepository;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    public function hasUserAlreadyReviewedEquipment(int $equipmentId){
        $user = auth()->user();

        $reviews = DB::table('reviews')->where('user_id', $user->id)->get();

        $isReviewOnAnEquipmentAlreadyReviewed = false;

        foreach($reviews as $review){
            $rental = findOrFail($equipmentId);

            if($equipmentId == $rental->id){
                $isReviewOnAnEquipmentAlreadyReviewed = true;
            }
        }

        return $isReviewOnAnEquipmentAlreadyReviewed;
    }
}
