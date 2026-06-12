<?php

namespace App\Admin\Http\Controllers\FlashSale;

use App\Admin\DataTables\FlashSale\FlashSaleDataTable;
use App\Admin\Http\Controllers\Controller;
use App\Admin\Http\Requests\FlashSale\FlashSaleRequest;
use App\Admin\Repositories\FlashSale\FlashSaleRepositoryInterface;
use App\Admin\Repositories\Product\ProductRepositoryInterface;
use App\Admin\Services\FlashSale\FlashSaleServiceInterface;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;

class FlashSaleController extends Controller
{
    protected $repository;
    protected ProductRepositoryInterface $productRepository;

    public function __construct(
        FlashSaleRepositoryInterface $repository,
        FlashSaleServiceInterface    $service,
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
            'index' => 'admin.flash_sales.index',
            'create' => 'admin.flash_sales.create',
            'edit' => 'admin.flash_sales.edit',
            'add_item_product' => 'admin.flash_sales.partials.add-item-product',
        ];
    }

    public function getRoute(): array
    {

        return [
            'index' => 'admin.flashsale.index',
            'create' => 'admin.flashsale.create',
            'edit' => 'admin.flashsale.edit',
        ];
    }

    public function index(FlashSaleDataTable $dataTable)
    {

        return $dataTable->render($this->view['index'], [
            'breadcrumbs' => $this->crums->add(__('Danh sách Flash Sale'))
        ]);
    }


    public function create(): Factory|View|Application
    {
        return $this->renderView(
            $this->view['create'],
            $this->crums->add(__('Danh sách Flash Sale'), route($this->route['index']))->add(__('add'))
        );
    }


    public function edit($id): Factory|View|Application
    {
        $instance = $this->repository->findOrFail($id);

        return $this->renderView(
            $this->view['edit'],
            $this->crums->add(__('Danh sách Flash Sale'), route($this->route['index']))->add(__('edit')),
            ['instance' => $instance]
        );
    }


    public function store(FlashSaleRequest $request)
    {
        return $this->handleStoreResponse($request, function ($request) {
            return $this->service->store($request);
        }, $this->route['edit']);
    }

    public function update(FlashSaleRequest $request)
    {
        return $this->handleUpdateResponse($request, function ($request) {
            return $this->service->update($request);
        });
    }

    public function delete($id)
    {
        return $this->handleDeleteResponse($id, function ($id) {
            return $this->service->delete($id);
        }, $this->route['index']);
    }

    public function addProduct(FlashSaleRequest $request): JsonResponse
    {
        $slug = $request->input('product_slug');
        $product = $this->productRepository->findByField('slug', $slug);

        if (!$product) {
            return response()->json([
                'status' => 400,
                'message' => __('fail')
            ], 400);
        }
        // Nếu có product_variation_id thì gán đúng biến thể vào quan hệ tạm thời
        $variationId = $request->input('product_variation_id');
        if (!empty($variationId)) {
            $variation = $product->productVariations()->where('id', $variationId)->first();
            if (!$variation) {
                return response()->json([
                    'status' => 400,
                    'message' => __('fail')
                ], 400);
            }
            // Gán quan hệ để blade sử dụng `$product->product_variation`
            $product->setRelation('product_variation', $variation);
        }
        $response = view($this->view['add_item_product'], compact('product'))->render();

        return response()->json([
            'status' => 200,
            'message' => __('success'),
            'data' => $response
        ], 200);
    }

    public function deleteDetail($id)
    {
        if ($this->repository->deleteDetail($id)) {
            return response()->json([
                'status' => 200,
                'msg' => __('success')
            ], 200);
        }
        return response()->json([
            'status' => 400,
            'msg' => __('fail')
        ], 400);
    }
}
