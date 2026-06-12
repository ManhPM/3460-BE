<?php

namespace App\Api\V1\Http\Resources\PostCategory;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Api\V1\Repositories\Post\PostRepositoryInterface;
use App\Api\V1\Http\Resources\Post\AllPostResource;

class ShowCategoryWithPostResource extends JsonResource
{
    protected $repositoryPost;

    public function __construct($resource, PostRepositoryInterface $repositoryPost)
    {
        parent::__construct($resource);
        $this->repositoryPost = $repositoryPost;
    }
    public function toArray($request)
    {
        $data = [
            'id' => $this->id,
            'name' => $this->name
        ];
        $array_id = array_column($this->descendants->toArray(), 'id');
        array_push($array_id, $this->id);
        $posts = $this->repositoryPost->getByCategoriesIdsPaginate($array_id, ...$request->only('page', 'limit'));
        $data['posts'] = new AllPostResource($posts);
        return $data;
    }
}
