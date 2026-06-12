<?php

namespace App\Api\V1\Http\Resources\Category;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Api\V1\Repositories\Product\ProductRepositoryInterface;
use App\Api\V1\Http\Resources\Product\AllProductResource;
use App\Api\V1\Http\Resources\Product\AllProductResourceNoPaginate;

class RootCategoryWithProductResource extends ResourceCollection
{
    protected $repositoryProduct;

    public function __construct($resource, ProductRepositoryInterface $repositoryProduct)
    {
        parent::__construct($resource);
        $this->repositoryProduct = $repositoryProduct;
    }
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($category) {
            $data = [
                'id' => $category->id,
                'name' => $category->name,
            ];

            $array_id = array_column($category->descendants->toArray(), 'id');
            array_push($array_id, $category->id);

            $products = $this->repositoryProduct->getByCategoriesWithRelations($array_id);

            $data['products'] = new AllProductResourceNoPaginate($products);
            return $data;
        });
    }
}
