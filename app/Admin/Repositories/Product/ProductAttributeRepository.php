<?php

namespace App\Admin\Repositories\Product;

use App\Admin\Repositories\EloquentRepository;
use App\Admin\Repositories\Product\ProductAttributeRepositoryInterface;
use App\Models\ProductAttribute;

class ProductAttributeRepository extends EloquentRepository implements ProductAttributeRepositoryInterface
{

    protected $select = [];

    public function getModel()
    {
        return ProductAttribute::class;
    }
    public function createOrUpdateWithVariation($product_id, array $productAttribute)
    {
        if (isset($productAttribute['attribute_id']) && is_array($productAttribute['attribute_id'])) {
            foreach ($productAttribute['attribute_id'] as $key => $value) {
                $variations = $productAttribute['attribute_variation_id'][$value] ?? [];
                $this->model->updateOrCreate([
                    'product_id' => $product_id,
                    'attribute_id' => $value,
                ], [
                    'position' => $key
                ])->attribute_variations()
                    ->sync($variations);
            }
        }
    }

    public function createOrUpdateWithVariationApi($product_id, array $productAttribute)
    {
        foreach ($productAttribute['attribute_id'] as $key => $value) {
            $this->model->updateOrCreate([
                'product_id' => $product_id,
                'attribute_id' => $value,
            ], [
                'position' => $key
            ])->attribute_variations()
                ->sync($productAttribute['attribute_variation_id'][$key]);
        }
    }

    public function delete($id)
    {
        $this->findOrFail($id);
        if ($this->instance) {
            $this->instance->delete();
            return true;
        }
        return false;
    }

    public function getQueryBuilderOrderBy($column = 'id', $sort = 'DESC')
    {
        $this->getQueryBuilder();
        $this->instance = $this->instance->orderBy($column, $sort);
        return $this->instance;
    }
}
