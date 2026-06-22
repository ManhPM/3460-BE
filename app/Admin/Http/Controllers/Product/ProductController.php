<?php

namespace App\Admin\Http\Controllers\Product;

use App\Admin\Http\Controllers\Controller;
use App\Admin\Http\Requests\Product\ProductRequest;
use App\Admin\Http\Resources\Product\ProductEditResource;
use App\Admin\Repositories\Product\ProductRepositoryInterface;
use App\Admin\Services\Product\ProductServiceInterface;
use App\Admin\DataTables\Product\ProductDataTable;
use App\Enums\Product\ProductType;
use App\Admin\Repositories\Category\CategoryRepositoryInterface;
use App\Admin\Repositories\Attribute\AttributeRepositoryInterface;
use App\Admin\Repositories\Discount\DiscountRepositoryInterface;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Exports\ProductTemplateExport;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends Controller
{
    protected CategoryRepositoryInterface $categoryRepository;
    protected AttributeRepositoryInterface $attributeRepository;
    protected DiscountRepositoryInterface $discountRepository;

    public function __construct(
        ProductRepositoryInterface   $repository,
        DiscountRepositoryInterface  $discountRepository,
        CategoryRepositoryInterface  $categoryRepository,
        AttributeRepositoryInterface $attributeRepository,
        ProductServiceInterface      $service
    ) {
        parent::__construct();
        $this->repository = $repository;
        $this->categoryRepository = $categoryRepository;
        $this->attributeRepository = $attributeRepository;
        $this->discountRepository = $discountRepository;
        $this->service = $service;
    }

    public function getView(): array
    {
        return [
            'index' => 'admin.products.index',
            'create' => 'admin.products.create',
            'edit' => 'admin.products.edit',
            'search_render_list' => 'admin.orders.partials.list-search-result-product',
            'search_render_flash_sale' => 'admin.flash_sales.partials.list-search-result-product'
        ];
    }

    public function getRoute(): array
    {
        return [
            'index' => 'admin.product.index',
            'create' => 'admin.product.create',
            'edit' => 'admin.product.edit',
            'delete' => 'admin.product.delete'
        ];
    }

    public function index(ProductDataTable $dataTable)
    {
        $categories = $this->categoryRepository->getFlatTree();
        $categories = $categories->map(function ($category) {
            return [$category->id => generate_text_depth_tree($category->depth) . $category->name];
        });
        return $dataTable->render($this->view['index'], [
            'categories' => $categories,
            'breadcrumbs' => $this->crums->add(__('Danh sách sản phẩm'))
        ]);
    }

    public function create(): Factory|View|Application
    {
        return $this->renderView(
            $this->view['create'],
            $this->crums->add(__('Danh sách sản phẩm'), route($this->route['index']))->add(__('add')),
            [
                'type' => ProductType::asSelectArray(),
                'categories' => $this->categoryRepository->getFlatTree(),
                'attributes' => $this->attributeRepository->getAllPluckById(),
            ]
        );
    }

    public function edit($id, Request $request)
    {
        $product = $this->repository->loadRelations($this->repository->findOrFail($id), [
            'categories:id',
            'productAttributes' => function ($query) {
                return $query->with(['attribute.variations', 'attributeVariations:id']);
            },
            'productVariations.attributeVariations'
        ]);

        // Abort if branch admin tries to access other admin's product
        $admin = auth('admin')->user();
        if ($admin instanceof \App\Models\Admin && $admin->hasRole('branch')) {
            if ((int)$product->admin_id !== (int)$admin->id) {
                abort(403);
            }
        }

        return $this->renderView(
            $this->view['edit'],
            $this->crums->add(__('Danh sách sản phẩm'), route($this->route['index']))->add(__('edit')),
            [
                'product' => (object)(new ProductEditResource($product))->toArray($request),
                'type' => ProductType::asSelectArray(),
                'categories' => $this->categoryRepository->getFlatTree(),
                'attributes' => $this->attributeRepository->getAllPluckById(),
            ]
        );
    }


    public function store(ProductRequest $request): RedirectResponse
    {
        return $this->handleStoreResponse($request, function ($request) {
            return $this->service->store($request);
        }, $this->route['edit']);
    }

    public function update(ProductRequest $request): RedirectResponse
    {
        return $this->handleUpdateResponse($request, function ($request) {
            return $this->service->update($request);
        });
    }

    public function delete($id): RedirectResponse
    {
        // Abort if branch admin tries to delete other admin's product
        $admin = auth('admin')->user();
        if ($admin instanceof \App\Models\Admin && $admin->hasRole('branch')) {
            $product = $this->repository->findOrFail($id);
            if ((int)$product->admin_id !== (int)$admin->id) {
                abort(403);
            }
        }

        return $this->handleDeleteResponse($id, function ($id) {
            return $this->service->delete($id);
        }, $this->route['index']);
    }

    public function searchRenderProductAndVariationOrder(Request $request): Factory|View|Application
    {
        $products = $this->repository->getByColumnsWithRelationsLimit([
            'name' => $request->input('key')
        ]);
        return view($this->view['search_render_list'], compact('products'));
    }

    public function searchRenderProductFlashSale(Request $request): Factory|View|Application
    {
        $products = $this->repository->getAllByColumns([
            'name' => $request->input('key'),

        ]);
        return view($this->view['search_render_flash_sale'], compact('products'));
    }

    public function checkProductFlashSale($slug)
    {
        $product = $this->repository->findByField('slug', $slug);
        if (!$product) {
            return response()->json([
                'status' => false,
            ], 404);
        }
        if ($product->is_flash_sale) {
            return response()->json([
                'status' => false,
            ], 404);
        }
        return response()->json([
            'status' => true,
        ], 200);
    }

    public function export()
    {
        return Excel::download(new ProductTemplateExport, 'mau_nhap_san_pham.xlsx');
    }

    public function import(Request $request)
    {
        return $this->handleStoreResponse($request, function ($request) {
            $count = $this->service->import($request);
            return (object) ['id' => $count];
        }, null);
    }

    public function clear()
    {
        return $this->handleDeleteResponse(null, function () {
            return $this->service->clearAllData();
        }, $this->route['index']);
    }

    public function seed()
    {
        return $this->handleStoreResponse(request(), function () {
            return $this->service->seedData();
        }, null);
    }
}
