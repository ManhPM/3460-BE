<?php

namespace App\Http\Controllers\Wishlist;

use App\Http\Controllers\Controller;
use App\Admin\Traits\AuthService;
use App\Admin\Repositories\Wishlist\WishlistRepositoryInterface;
use App\Api\V1\Support\Response;

class WishlistController extends Controller
{
    use AuthService, Response;

    protected $repository;

    public function __construct(WishlistRepositoryInterface $repository)
    {
        parent::__construct();
        $this->repository = $repository;
    }

    public function getView(): array
    {
        return [
            'index' => 'user.auth.wishlist',
        ];
    }

    public function index()
    {
        return view($this->view['index'], [
            'breadcrumbs' => $this->crums->add(__('Danh sách yêu thích'))->getBreadcrumbs()
        ]);
    }

    public function toggle($id)
    {
        $user = $this->getCurrentUser();

        $wishlist = $this->repository->getBy(['product_id' => $id, 'user_id' => $user->id])->first();
        if ($wishlist) {
            $wishlist->delete();
            return $this->jsonResponseSuccess(['is_wishlist' => false]);
        }
        $this->repository->create(['product_id' => $id, 'user_id' => $user->id]);
        return $this->jsonResponseSuccess(['is_wishlist' => true]);
    }

    public function delete($id)
    {
        $user = $this->getCurrentUser();
        $wishlist = $this->repository->find($id);
        if ($wishlist && $wishlist->user_id == $user->id) {
            $wishlist->delete();
            return $this->jsonResponseSuccess(null);
        }
        return $this->jsonResponseError();
    }
}
