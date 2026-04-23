<?php

namespace App\Repository\Eloquent;

use App\Repository\ReviewRepositoryInterface;
use App\Repository\Eloquent\BaseRepository;
use App\Models\Review;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ReviewRepository extends BaseRepository implements ReviewRepositoryInterface
{
    public function __construct(Review $model)
    {
        parent::__construct($model);
    }
}
