<?php

namespace App\Admin\Repositories\Wishlist;

use App\Admin\Repositories\EloquentRepository;
use App\Models\Wishlist;

class WishlistRepository extends EloquentRepository implements WishlistRepositoryInterface
{
    public function getModel(): string
    {
        return Wishlist::class;
    }
}
