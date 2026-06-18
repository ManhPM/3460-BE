<?php

namespace App\Admin\Services\Product;

use App\Admin\Services\Product\ProductServiceInterface;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use App\Admin\Repositories\Product\{
    ProductRepositoryInterface,
    ProductAttributeRepositoryInterface,
    ProductVariationRepositoryInterface
};
use Illuminate\Http\Request;
use App\Admin\Traits\Setup;
use Illuminate\Support\Facades\DB;
use App\Admin\Repositories\AttributeVariation\AttributeVariationRepositoryInterface;
use App\Admin\Services\File\FileService;
use App\Enums\Product\ProductType;
use App\Enums\Product\ProductVariationAction;
use App\Traits\UseLog;
use Illuminate\Support\Facades\Log;
use Throwable;


class ProductService implements ProductServiceInterface
{
    use Setup, UseLog;


    protected array $data;

    protected $repository;
    protected $repositoryAttributeVariation;
    protected $repositoryProductAttribute;
    protected $repositoryProductVariation;
    protected $fileService;

    public function __construct(
        ProductRepositoryInterface $repository,
        AttributeVariationRepositoryInterface $repositoryAttributeVariation,
        ProductAttributeRepositoryInterface $repositoryProductAttribute,
        ProductVariationRepositoryInterface $repositoryProductVariation,
        FileService $fileService
    ) {
        $this->repository = $repository;
        $this->repositoryAttributeVariation = $repositoryAttributeVariation;
        $this->repositoryProductAttribute = $repositoryProductAttribute;
        $this->repositoryProductVariation = $repositoryProductVariation;
        $this->fileService = $fileService;
    }

    public function store(Request $request)
    {
        $this->data = $request->validated();
        $this->data['product']['gallery'] = $this->data['product']['gallery'] ? explode(",", $this->data['product']['gallery']) : null;
        $instance = $this->repository->create($this->data['product']);
        $this->repository->attachCategories($instance, $this->data['categories_id'] ?? []);
        if ($instance->type == ProductType::Variable && isset($this->data['product_attribute'])) {
            $this->repositoryProductAttribute->createOrUpdateWithVariation($instance->id, $this->data['product_attribute']);

            $this->storeOrUpdateProductVariations($instance->id);
        }
        return $instance;
    }

    public function update(Request $request): object|bool
    {

        $this->data = $request->validated();

        DB::beginTransaction();
        try {
            $instance = $this->repository->find($this->data['product']['id']);
            $this->data['product']['gallery'] = $this->data['product']['gallery'] ? explode(",", $this->data['product']['gallery']) : null;
            $instance = $this->repository->update($this->data['product']['id'], $this->data['product']);
            $this->repository->syncCategories($instance, $this->data['categories_id'] ?? []);

            if ($instance->type == ProductType::Variable && isset($this->data['product_attribute'])) {
                $this->repositoryProductAttribute->createOrUpdateWithVariation($instance->id, $this->data['product_attribute']);
                $this->storeOrUpdateProductVariations($instance->id);
            } else {
                $this->repository->deleteProductAttributes($instance);
                $this->repository->deleteProductVariations($instance);
            }
            DB::commit();
            return $instance;
        } catch (Exception $e) {
            throw $e;
            DB::rollBack();
            return false;
        }
    }

    public function updateApi(Request $request): object|bool
    {

        $this->data = $request->validated();

        DB::beginTransaction();
        try {
            $instance = $this->repository->update($this->data['product']['id'], $this->data['product']);
            $this->repository->syncCategories($instance, $this->data['categories_id'] ?? []);
            $this->repository->syncToppings($instance, $this->data['toppings_id'] ?? []);
            $this->repository->syncDiscounts($instance, $this->data['discount_ids'] ?? []);


            if ($instance->type == ProductType::Variable && isset($this->data['product_attribute'])) {
                $this->repositoryProductAttribute->createOrUpdateWithVariationApi($instance->id, $this->data['product_attribute']);
                $this->storeOrUpdateProductVariations($instance->id);
            } else {
                $this->repository->deleteProductAttributes($instance);
                $this->repository->deleteProductVariations($instance);
            }
            DB::commit();
            return $instance;
        } catch (Throwable $th) {
            DB::rollBack();
            return false;
        }
    }


    public function delete($id): object|bool
    {
        return $this->repository->update($id, ['is_active' => 0]);
    }

    protected function storeOrUpdateProductVariations($product_id): void
    {
        if (isset($this->data['products_variations']['attribute_variation_id']) && $this->data['products_variations']['attribute_variation_id']) {
            $attribute_variation_id = collect($this->data['product_attribute']['attribute_variation_id'])->collapse()->flip();

            foreach ($this->data['products_variations']['attribute_variation_id'] as $key => $item) {
                if (!$attribute_variation_id->has($item)) {
                    unset($this->data['products_variations']['attribute_variation_id'][$key]);
                }
            }
            $this->repositoryProductVariation->createOrUpdateWithVariation($product_id, $this->data['products_variations']);
        }
    }

    public function createProductVariations(Request $request, array $view): View|Factory|string|Application
    {

        $data = $request->validated();

        $attribute_variations = $this->repositoryAttributeVariation->getOrderByFollow($data['product_attribute']['attribute_variation_id']);
        if ($data['variation_action'] == ProductVariationAction::AddSimple) {
            $response = view($view['product_variation'], [
                'attribute_variations' => $attribute_variations,
                'identity' => $this->uniqidReal(5)
            ]);
        } elseif ($data['variation_action'] == ProductVariationAction::AddFromAllVariations) {
            $collect = collect($attribute_variations[0]->keys()->all());
            $arr = [];

            foreach ($attribute_variations as $key => $attributeVariation) {
                if ($key != 0) {
                    $arr[] = $attributeVariation->keys()->all();
                }
            }
            $collect = $collect->crossJoin(...$arr);
            $response = '';
            foreach ($collect as $item) {
                $response .= view($view['product_variation'], [
                    'attribute_variations' => $attribute_variations,
                    'identity' => $this->uniqidReal(5),
                    'selected' => $item
                ])->render();
            }
            return $response;
        } else {
            $response = view($view['no_variation']);
        }
        return $response;
    }

    public function clearAllData()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('products')->truncate();
        DB::table('products_categories')->truncate();
        DB::table('products_attributes')->truncate();
        DB::table('products_variations')->truncate();
        DB::table('products_attributes_variations')->truncate();
        DB::table('products_variations_variations')->truncate();
        DB::table('admin_inventories')->truncate();
        DB::table('flash_sales_products')->truncate();
        DB::table('reviews')->truncate();
        DB::table('wishlists')->truncate();
        DB::table('shopping_cart')->truncate();
        DB::table('order_details')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        return true;
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls', 'max:10240'],
        ]);

        $file = $request->file('file');
        $data = \Maatwebsite\Excel\Facades\Excel::toArray(new class implements \Maatwebsite\Excel\Concerns\WithCalculatedFormulas {}, $file);

        $rows = $data[0] ?? [];
        array_shift($rows); // bỏ dòng header

        if (empty($rows)) {
            throw new Exception('File không có dữ liệu.');
        }

        $count = 0;
        foreach ($rows as $row) {
            $name = trim($row[0] ?? '');
            $price = trim($row[1] ?? 0);
            $promotionPrice = trim($row[2] ?? 0);
            $qty = trim($row[3] ?? 0);
            $desc = trim($row[4] ?? '');

            if (empty($name)) {
                continue;
            }

            // Tạo sản phẩm
            $productData = [
                'name' => $name,
                'price' => (float)$price,
                'promotion_price' => empty($promotionPrice) ? null : (float)$promotionPrice,
                'desc' => $desc,
                'type' => ProductType::Simple,
                'is_active' => 1,
                'is_featured' => \App\Enums\DefaultActiveStatus::Active,
            ];

            $product = $this->repository->create($productData);

            // Lưu số lượng vào tồn kho của chi nhánh hiện tại
            $adminId = auth('admin')->id();
            if ($adminId) {
                \App\Models\AdminInventory::updateOrCreate([
                    'admin_id' => $adminId,
                    'product_id' => $product->id,
                    'product_variation_id' => null,
                ], [
                    'qty' => (int)$qty
                ]);
            }

            $count++;
        }

        return $count;
    }
}
