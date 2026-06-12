<?php

namespace App\Admin\Http\Controllers\ProductCategory;

use App\Admin\DataTables\ProductCategory\ProductCategoryDataTable;
use App\Admin\Http\Controllers\Controller;
use App\Admin\Http\Requests\Category\ProductCategoryRequest;
use App\Admin\Repositories\Category\CategoryRepositoryInterface;
use App\Admin\Services\Category\CategoryServiceInterface;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use App\Admin\DataTables\Product\ProductDataTable;
use App\Admin\Repositories\Product\ProductRepositoryInterface;

class ProductCategoryController extends Controller
{
    protected $productRepository;

    public function __construct(
        CategoryRepositoryInterface $repository,
        CategoryServiceInterface $service,
        ProductRepositoryInterface $productRepository,
    ) {

        parent::__construct();

        $this->repository = $repository;

        $this->service = $service;

        $this->productRepository = $productRepository;
    }

    public function getView(): array
    {
        return [
            'index' => 'admin.categories.index',
            'create' => 'admin.categories.create',
            'edit' => 'admin.categories.edit',
            'product' => 'admin.categories.product',
        ];
    }

    public function getRoute(): array
    {
        return [
            'index' => 'admin.category.index',
            'create' => 'admin.category.create',
            'edit' => 'admin.category.edit',
            'delete' => 'admin.category.delete',
            'product' => 'admin.category.product',
        ];
    }

    public function index(ProductCategoryDataTable $dataTable)
    {
        return $dataTable->render($this->view['index'], [
            'active' => [
                'Hoạt động' => 'Hoạt động',
                'Ẩn' => 'Ẩn'
            ],
            'breadcrumbs' => $this->crums->add(__('Danh sách danh mục'))
        ]);
    }

    public function create(): Factory|View|Application
    {
        return $this->renderView(
            $this->view['create'],
            $this->crums->add(__('Danh sách danh mục'), route($this->route['index']))->add(__('add')),
            [
                'categories' => $this->repository->getFlatTree(),
            ]
        );
    }

    public function edit($id): Factory|View|Application
    {
        return $this->renderView(
            $this->view['edit'],
            $this->crums->add(__('Danh sách danh mục'), route($this->route['index']))->add(__('edit')),
            [
                'category' => $this->repository->findOrFail($id),
                'categories' => $this->repository->getFlatTreeNotInNode([$id]),
            ]
        );
    }


    public function product($id, ProductDataTable $dataTable)
    {
        $categories = $this->repository->getFlatTree();
        $categories = $categories->map(function ($category) {
            return [$category->id => generate_text_depth_tree($category->depth) . $category->name];
        });

        $category = $this->repository->findOrFail($id);

        $productIds = $this->productRepository->getQueryBuilderOrderBy()->whereHas('categories', function ($query) use ($id) {
            $query->where('categories.id', $id);
        })->get()->pluck('id')->toArray();

        if ($productIds) {
            $dataTable = new ProductDataTable($this->productRepository, $this->repository, $productIds);
        } else {
            $dataTable = new ProductDataTable($this->productRepository, $this->repository, [-1]);
        }

        return $dataTable->render($this->view['product'], [
            'categories' => $categories,
            'category' => $category,
            'breadcrumbs' => $this->crums->add(__('Danh sách sản phẩm danh mục'))
        ]);
    }

    public function store(ProductCategoryRequest $request): RedirectResponse
    {
        return $this->handleStoreResponse($request, function ($request) {
            return $this->service->store($request);
        }, $this->route['edit']);
    }

    public function update(ProductCategoryRequest $request): RedirectResponse
    {
        return $this->handleUpdateResponse($request, function ($request) {
            return $this->service->update($request);
        });
    }

    public function delete($id): RedirectResponse
    {
        return $this->handleDeleteResponse($id, function ($id) {
            return $this->service->delete($id);
        }, $this->route['index']);
    }
}
