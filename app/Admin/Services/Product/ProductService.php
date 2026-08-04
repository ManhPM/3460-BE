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
        if ($instance->type == ProductType::Variable) {
            if (isset($this->data['product_attribute'])) {
                $this->repositoryProductAttribute->createOrUpdateWithVariation($instance->id, $this->data['product_attribute']);
            }
            if (isset($this->data['products_variations'])) {
                $this->storeOrUpdateProductVariations($instance->id);
            }
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

            if ($instance->type == ProductType::Variable) {
                if (isset($this->data['product_attribute'])) {
                    $this->repositoryProductAttribute->createOrUpdateWithVariation($instance->id, $this->data['product_attribute']);
                }
                if (isset($this->data['products_variations'])) {
                    $this->storeOrUpdateProductVariations($instance->id);
                }
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


            if ($instance->type == ProductType::Variable) {
                if (isset($this->data['product_attribute'])) {
                    $this->repositoryProductAttribute->createOrUpdateWithVariationApi($instance->id, $this->data['product_attribute']);
                }
                if (isset($this->data['products_variations'])) {
                    $this->storeOrUpdateProductVariations($instance->id);
                }
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
            $rawAttrVars = $this->data['product_attribute']['attribute_variation_id'] ?? [];
            $attribute_variation_id = collect($rawAttrVars)->collapse()->flip();

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

        $attribute_variations = $this->repositoryAttributeVariation->getOrderByFollow($data['product_attribute']['attribute_variation_id'] ?? []);
        if ($data['variation_action'] == ProductVariationAction::AddSimple) {
            $response = view($view['product_variation'], [
                'attribute_variations' => $attribute_variations,
                'identity' => $this->uniqidReal(5)
            ]);
        } elseif ($data['variation_action'] == ProductVariationAction::AddFromAllVariations) {
            if ($attribute_variations->isEmpty() || !isset($attribute_variations[0])) {
                return view($view['no_variation']);
            }
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
            $rawQty = $row[3] ?? 0;
            $qty = (is_numeric($rawQty) && (float)$rawQty == (int)$rawQty) ? (int)$rawQty : 0;
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
                'avatar' => '/public/assets/images/logo.png',
                'type' => ProductType::Simple,
                'is_active' => 1,
                'is_featured' => \App\Enums\DefaultActiveStatus::Active,
            ];

            $product = $this->repository->create($productData);

            // Lưu số lượng vào tồn kho của chi nhánh
            $admin = auth('admin')->user();
            if ($admin) {
                if ($admin->id == 1 || $admin->hasRole('superAdmin')) {
                    // Nếu là super admin, gán tồn kho cho tất cả các chi nhánh (role branch)
                    $branches = \App\Models\Admin::whereHas('roles', function ($q) {
                        $q->where('name', 'branch');
                    })->get();

                    foreach ($branches as $branch) {
                        \App\Models\AdminInventory::updateOrCreate([
                            'admin_id' => $branch->id,
                            'product_id' => $product->id,
                            'product_variation_id' => null,
                        ], [
                            'qty' => $qty
                        ]);
                    }
                } else {
                    // Nếu là admin chi nhánh, gán tồn kho cho chính chi nhánh đó
                    \App\Models\AdminInventory::updateOrCreate([
                        'admin_id' => $admin->id,
                        'product_id' => $product->id,
                        'product_variation_id' => null,
                    ], [
                        'qty' => $qty
                    ]);
                }
            }

            $count++;
        }

        return $count;
    }

    public function seedData()
    {
        $productsData = [
            [
                'name' => 'Sữa tươi tiệt trùng Vinamilk 1L',
                'price' => 35000,
                'promotion_price' => 32000,
                'desc' => 'Sữa tươi tiệt trùng Vinamilk 100% Sữa tươi hộp 1L giàu dưỡng chất, vitamin và khoáng chất tự nhiên tốt cho sức khỏe.',
                'avatar' => '/public/uploads/seed_milk.png',
                'category_name' => 'Sữa',
            ],
            [
                'name' => 'Mì ăn liền Hảo Hảo Tôm Chua Cay',
                'price' => 4500,
                'promotion_price' => 4000,
                'desc' => 'Mì ăn liền Hảo Hảo hương vị Tôm Chua Cay truyền thống, sợi mì dai ngon hòa quyện nước súp chua cay đậm đà.',
                'avatar' => '/public/uploads/seed_noodles.png',
                'category_name' => 'Mì ăn liền , bún, miến , phở',
            ],
            [
                'name' => 'Nước rửa chén Sunlight Chanh 750g',
                'price' => 28000,
                'promotion_price' => 25000,
                'desc' => 'Nước rửa chén Sunlight Chanh đánh bay dầu mỡ nhanh chóng với sức mạnh từ chanh tươi tự nhiên, an toàn cho da tay.',
                'avatar' => '/public/uploads/seed_dishwash.png',
                'category_name' => 'Các sản phẩm khác',
            ],
            [
                'name' => 'Trứng gà ta hộp 10 quả',
                'price' => 40000,
                'promotion_price' => 35000,
                'desc' => 'Hộp 10 quả trứng gà ta sạch được tuyển chọn kỹ lưỡng, giàu protein và dưỡng chất cho bữa ăn gia đình.',
                'avatar' => '/public/uploads/seed_eggs.png',
                'category_name' => 'Các sản phẩm khác',
            ],
            [
                'name' => 'Dầu ăn Simply đậu nành 1L',
                'price' => 65000,
                'promotion_price' => 60000,
                'desc' => 'Dầu ăn Simply nguyên chất 100% từ hạt đậu nành tự nhiên, giàu Omega-3, 6, 9 tốt cho sức khỏe tim mạch.',
                'avatar' => '/public/uploads/seed_cooking_oil.png',
                'category_name' => 'Gia vị , nguyên liệu',
            ],
            [
                'name' => 'Bánh mì gối cắt lát siêu mềm',
                'price' => 22000,
                'promotion_price' => 20000,
                'desc' => 'Bánh mì gối cắt lát thơm ngon, mềm mịn, thích hợp làm bữa sáng tiện lợi, nhanh chóng cho cả nhà.',
                'avatar' => '/public/uploads/seed_bread.png',
                'category_name' => 'Các sản phẩm khác',
            ],
            [
                'name' => 'Khoai tây chiên Lay\'s vị tự nhiên',
                'price' => 18000,
                'promotion_price' => 16000,
                'desc' => 'Snack khoai tây chiên Lay\'s Classic giòn rụm từ khoai tây tươi tự nhiên chọn lọc kết hợp muối biển.',
                'avatar' => '/public/uploads/seed_chips.png',
                'category_name' => 'Bánh kẹo',
            ],
            [
                'name' => 'Nước tinh khiết Aquafina 500ml',
                'price' => 6000,
                'promotion_price' => 5500,
                'desc' => 'Nước uống tinh khiết Aquafina được xử lý qua hệ thống lọc tối tân Hydro-7, mang lại sự tinh khiết sảng khoái.',
                'avatar' => '/public/uploads/seed_water.png',
                'category_name' => 'Nước ngọt',
            ],
        ];

        foreach ($productsData as $data) {
            $categoryName = $data['category_name'];
            $category = \App\Models\Category::where('name', 'like', "%{$categoryName}%")->first();
            if (!$category) {
                $slug = \Illuminate\Support\Str::slug($categoryName);
                $originalSlug = $slug;
                $count = 1;
                while (\App\Models\Category::where('slug', $slug)->exists()) {
                    $slug = $originalSlug . '-' . $count++;
                }
                $category = \App\Models\Category::create([
                    'name' => $categoryName,
                    'slug' => $slug,
                    'is_active' => 1,
                    'is_home' => 1,
                ]);
            }

            $slug = \Illuminate\Support\Str::slug($data['name']);
            $originalProductSlug = $slug;
            $count = 1;
            while ($this->repository->getModel()::where('slug', $slug)->exists()) {
                $slug = $originalProductSlug . '-' . $count++;
            }

            $product = $this->repository->create([
                'name' => $data['name'],
                'slug' => $slug,
                'price' => $data['price'],
                'promotion_price' => $data['promotion_price'],
                'desc' => $data['desc'],
                'avatar' => $data['avatar'],
                'type' => ProductType::Simple,
                'is_active' => 1,
                'is_featured' => \App\Enums\DefaultActiveStatus::Active,
            ]);

            $this->repository->attachCategories($product, [$category->id]);

            $admins = \App\Models\Admin::all();
            foreach ($admins as $admin) {
                \App\Models\AdminInventory::updateOrCreate([
                    'admin_id' => $admin->id,
                    'product_id' => $product->id,
                    'product_variation_id' => null,
                ], [
                    'qty' => 100
                ]);
            }
        }

        return true;
    }
}
